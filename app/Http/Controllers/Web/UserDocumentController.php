<?php

namespace App\Http\Controllers\Web;

use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\File;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserDocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function defaultIndex(Request $request)
    {
        $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
        $data = new GetDocumentsData();
        $sortData = new DocumentSortData();
        return $this->index($request, $defaultDocumentType, $data, $sortData);
    }

    public function index(Request $request, string $documentType, GetDocumentsData $data, DocumentSortData $sortData)
    {
        $user = Auth::user();

        $docType = DocumentType::where(DocumentType::NAME, $documentType)->first();
        $defaultDocumentType = DocumentType::where('name', 'like', '%extern%')->first()->name;
        if (!$docType) {
            Auth::guard('web')->logout();
            $redirectTo = $request->fullUrl();
            $request->session()->put('redirect_to', $redirectTo);
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        $data->documentTypeId = $docType->id;
        $data->status = 'publicado';
        $documents = $this->documentService->list($data, $sortData, $user);

        // TODO: verificar si el usuario realmente no tiene permisos o si los filtros no devolvieron ningún documento
        /*if ($documents->total() == 0) {
            Auth::guard('web')->logout();
            $redirectTo = $request->fullUrl();
            $request->session()->put('redirect_to', $redirectTo);
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }*/

        return view('documents.index', [
            'documents' => $documents,
            'documentType' => $docType,
            'defaultDocumentType' => $defaultDocumentType,
            'filters' => [
                'keyword' => $request['keyword'],
                'number' => $request['number'],
                'date_from' => $request['date_from'],
                'date_to' => $request['date_to'],
                'sort' => $sortData->sort,
            ],
        ]);
    }

    public function show(Document $document): View
    {
        $user = Auth::user();

        try {
            $document = $this->documentService->get($document->id, $user);
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('documents.show', [
            'document' => $document,
            'user' => $user,
        ]);
    }

    public function downloadFile(Document $document, File $file): BinaryFileResponse
    {
        $user = Auth::user();

        try {
            $document = $this->documentService->get($document->id, $user);
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        if (! $document->files->contains(File::ID, $file->id)) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($file->path)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('local')->path($file->path),
            $file->name,
            ['Content-Type' => $file->mime_type]
        );
    }
}
