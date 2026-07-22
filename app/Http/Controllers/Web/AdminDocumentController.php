<?php

namespace App\Http\Controllers\Web;

use App\Data\CreateDocumentData;
use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminDocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request, GetDocumentsData $data, DocumentSortData $sortData): View
    {
        $user = Auth::user();
        $documents = $this->documentService->list($data, $sortData, $user);
        $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::DESCRIPTION)
            ->pluck(DocumentType::DESCRIPTION, DocumentType::ID);
        $statuses = [
            'borrador' => 'Borrador',
            'publicado' => 'Publicado',
        ];

        return view('admin.documents.index', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'statuses' => $statuses,
            'filters' => [
                'keyword' => $request['keyword'],
                'document_type_id' => $data->documentTypeId,
                'status' => $request['status'],
                'sort' => $sortData->sort,
            ],
        ]);
    }

    public function new(): View
    {
        $document = new Document;

        return $this->editOrNew($document);
    }

    public function show(Document $document): View
    {
        $user = Auth::user();
        try {
            $document = $this->documentService->get($document->id, $user);
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('admin.documents.show', [
            'document' => $document,
        ]);
    }

    public function update(CreateDocumentData $data, Request $request): View
    {
        $user = Auth::user();
        try {
            if ($data->id) {
                $document = $this->documentService->get($data->id, $user);
            } else {
                $document = new Document;
                $document->user_id = $user->id;
            }
            $document->fill($data->toArray());
            $document->save();

            // Sync authorized users
            $authorizedUserIds = array_filter(
                array_map('trim', explode(',', $request->input('authorized_user_ids', ''))),
                fn ($id) => is_numeric($id)
            );
            $document->authorizedUsers()->sync($authorizedUserIds);

            // Sync authorized groups
            $authorizedGroupIds = array_filter(
                array_map('trim', explode(',', $request->input('authorized_group_ids', ''))),
                fn ($id) => is_numeric($id)
            );
            $document->authorizedGroups()->sync($authorizedGroupIds);

            // Process removed files (from hidden input field)
            $removedFiles = $request->input('removed-files', '');
            if ($removedFiles) {
                $this->deleteRemovedFiles($document, $removedFiles);
            }

            // Process attached files
            if ($data->files && is_array($data->files)) {
                foreach ($data->files as $file) {
                    $this->saveAttachedFile($document, $file);
                }
            }
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return $this->editOrNew($document, 'Los datos de la normativa se actualizaron correctamente.');
    }

    public function edit(Document $document): View
    {
        $user = Auth::user();
        try {
            $document = $this->documentService->get($document->id, $user);
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return $this->editOrNew($document);
    }

    private function editOrNew(Document $document, ?string $message = null): View
    {
        $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::NAME)
            ->pluck('description', 'id');

        $users = User::query()
            ->orderBy(User::NAME)
            ->get();

        $groups = Group::query()
            ->orderBy(Group::DESCRIPTION)
            ->get();

        $statuses = [
            'borrador' => 'Borrador',
            'publicado' => 'Publicado',
        ];

        $files = File::where('document_id', $document->id)->get();

        $authorizedUserIds = $document->authorizedUsers->pluck('id')->implode(',');
        $authorizedGroupIds = $document->authorizedGroups->pluck('id')->implode(',');

        return view('admin.documents.form', [
            'document' => $document,
            'documentTypes' => $documentTypes,
            'statuses' => $statuses,
            'users' => $users,
            'groups' => $groups,
            'files' => $files,
            'message' => $message,
            'authorizedUserIds' => $authorizedUserIds,
            'authorizedGroupIds' => $authorizedGroupIds,
        ]);
    }

    /**
     * Save an attached file to the document
     *
     * @param  UploadedFile  $file
     */
    private function saveAttachedFile(Document $document, $file): void
    {
        if (! $file || ! is_object($file) || ! method_exists($file, 'getClientOriginalName')) {
            return;
        }

        $originalName = $file->getClientOriginalName();
        $normalizedPath = $this->normalizeFileName($originalName);
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        // Store file in local filesystem under documents/{document_id}/
        $storagePath = "documents/{$document->id}/{$normalizedPath}";
        Storage::disk('local')->put($storagePath, file_get_contents($file));

        // Create file record in database
        File::create([
            'document_id' => $document->id,
            'name' => $originalName,
            'path' => $storagePath,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);
    }

    /**
     * Delete files marked for removal
     *
     * @param  string  $removedFilesString  Comma-separated file IDs
     */
    private function deleteRemovedFiles(Document $document, string $removedFilesString): void
    {
        if (empty(trim($removedFilesString))) {
            return;
        }

        // Parse comma-separated IDs
        $fileIds = array_filter(
            array_map('trim', explode(',', $removedFilesString)),
            fn ($id) => is_numeric($id)
        );

        if (empty($fileIds)) {
            return;
        }

        // Delete each file
        foreach ($fileIds as $fileId) {
            $file = File::where('id', $fileId)
                ->where('document_id', $document->id)
                ->first();

            if ($file) {
                // Delete from filesystem
                if (Storage::disk('local')->exists($file->path)) {
                    Storage::disk('local')->delete($file->path);
                }

                // Delete database record
                $file->delete();
            }
        }
    }

    /**
     * Normalize file name by removing diacritics, replacing spaces with underscores, and converting to lowercase
     */
    private function normalizeFileName(string $fileName): string
    {
        // Separate name and extension
        $pathInfo = pathinfo($fileName);
        $name = $pathInfo['filename'] ?? '';
        $extension = isset($pathInfo['extension']) ? '.'.$pathInfo['extension'] : '';

        // Normalize: remove diacritics and convert special characters
        $normalized = $this->removeDiacritics($name);

        // Replace spaces with underscores
        $normalized = str_replace(' ', '_', $normalized);

        // Convert to lowercase
        $normalized = strtolower($normalized);

        return $normalized.$extension;
    }

    /**
     * Remove diacritical marks from a string
     */
    private function removeDiacritics(string $str): string
    {
        // Use PHP's Normalizer to decompose accented characters
        $str = normalizer_normalize($str, \Normalizer::FORM_D);

        // Remove all combining diacritical marks
        $str = preg_replace('/[\x{0300}-\x{036f}]/u', '', $str);

        // Handle special cases
        $replacements = [
            'ñ' => 'n',
            'Ñ' => 'n',
        ];

        return strtr($str, $replacements);
    }
}
