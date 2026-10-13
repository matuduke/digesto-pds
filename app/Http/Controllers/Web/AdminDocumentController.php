<?php

namespace App\Http\Controllers\Web;

use App\Data\CreateDocumentData;
use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Http\Controllers\Controller;
use App\Models\ChangeLog;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\File;
use App\Models\Group;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
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
            ->orderBy(DocumentType::DESCRIPTION, 'asc')
            ->pluck(DocumentType::DESCRIPTION, DocumentType::ID);
        $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
        $statuses = [
            'borrador' => 'Borrador',
            'publicado' => 'Publicado',
        ];

        return view('admin.documents.index', [
            'user' => $user,
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'statuses' => $statuses,
            'defaultDocumentType' => $defaultDocumentType,
            'filters' => [
                'keyword' => $request['keyword'],
                'number' => $request['number'],
                'document_type_id' => $data->documentTypeId,
                'status' => $request['status'],
                'date_from' => $request['date_from'],
                'date_to' => $request['date_to'],
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
            $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('admin.documents.show', [
            'document' => $document,
            'defaultDocumentType' => $defaultDocumentType,
            'user' => $user,
        ]);
    }

    public function update(CreateDocumentData $data, Request $request): RedirectResponse
    {
        $user = Auth::user();
        // Check user's permission
        if ($user->isCreator()) {
            try {
                if ($data->id) {
                    $document = $this->documentService->get($data->id, $user);
                } else {
                    $document = new Document;
                    $document->user_id = $user->id;
                }

                $isPublicChanged = false;
                if ($document->is_public != $data->isPublic) {
                    $isPublicChanged = true;
                }
                $document->fill($data->toArray());
                $changes = $document->getDirty();

                if (!$isPublicChanged) {
                    unset($changes['is_public']);
                }
                if (count($changes) > 0) {
                    if ($document->exists) {
                        $this->createLog($document, $user, json_encode($changes), 'document-updated');
                    } else {
                        $this->createLog($document, $user, 'La norma fue creada');
                    }

                }
                $document->save();

                // Remove html tags from external documents
                $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
                if ($document->documentType->name == $defaultDocumentType) {
                    $document->content = strip_tags($document->content, '<br>');
                    $document->content = str_replace('<br>', "<br>\n", $document->content);
                    $document->save();
                }

                // Sync authorized users
                $authorizedUserIds = array_filter(
                    array_map('trim', explode(',', $request->input('authorized_user_ids', ''))),
                    fn ($id) => is_numeric($id)
                );
                $syncedUsers = $document->authorizedUsers()->sync($authorizedUserIds);
                if (count($syncedUsers['attached']) > 0 || count($syncedUsers['detached']) > 0) {
                    $syncedUsers['detached'] = array_values($syncedUsers['detached']);
                    $this->createLog($document, $user, json_encode($syncedUsers), 'users-synched');
                }

                // Sync authorized groups
                $authorizedGroupIds = array_filter(
                    array_map('trim', explode(',', $request->input('authorized_group_ids', ''))),
                    fn ($id) => is_numeric($id)
                );
                $syncedGroups = $document->authorizedGroups()->sync($authorizedGroupIds);
                if (count($syncedGroups['attached']) > 0 || count($syncedGroups['detached']) > 0) {
                    $syncedGroups['detached'] = array_values($syncedGroups['detached']);
                    $this->createLog($document, $user, json_encode($syncedGroups), 'groups-synched');
                }

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
        } else {
            abort(403, 'Usted no pertenece al grupo de creadores de normativas');
        }

        return redirect()
            ->route('admin.documents.edit', ['document' => $document->id])
            ->with('message', 'Los datos de la normativa se actualizaron correctamente.');
    }

    public function setAsDraft(Document $document)
    {
        $user = Auth::user();

        // Check user's permission
        if ($user->isApprover()) {
            $document->status = 'borrador';
            $document->save();
            $this->createLog($document, $user, 'La normativa se cambió a estado borrador');
            return redirect()
            ->route('admin.documents.edit', ['document' => $document->id])
            ->with('message', 'La normativa se cambió a estado borrador.');
        } else {
            abort(403, 'Usted no pertenece al grupo de aprobadores de normativas');
        }
    }

    public function publish(Document $document)
    {
        $user = Auth::user();

        // Check user's permission
        if ($user->isApprover()) {
            $document->status = 'publicado';
            $document->save();
            $this->createLog($document, $user, 'La normativa se cambió a estado publicada');
            return redirect()
            ->route('admin.documents.edit', ['document' => $document->id])
            ->with('message', 'La normativa se cambió a estado publicada.');
        } else {
            abort(403, 'Usted no pertenece al grupo de aprobadores de normativas');
        }
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
        $user = Auth::user();

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
            'user' => $user,
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

    public function destroy(Document $document)
    {
        return $document->delete();
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

        $user = Auth::user();
        $this->createLog($document, $user, 'Se adjuntó el archivo: ' . $originalName);
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

            $user = Auth::user();
            $this->createLog($document, $user, 'Se eliminó el archivo: ' . $file->name);

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

    private function createLog(Document $document, User $user, string $changes, string $type = '')
    {
        ChangeLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'document_changes' => $changes,
            'type' => $type,
        ]);
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
