<?php

namespace App\Http\Controllers\Web;

use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserDocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request, string $documentType, GetDocumentsData $data, DocumentSortData $sortData)
    {

        $user = Auth::user();
        $docType = DocumentType::where(DocumentType::NAME, $documentType)->first();
        $data->documentTypeId = $docType->id;
        $data->status = 'publicado';
        $documents = $this->documentService->list($data, $sortData, $user);

        if ($documents->total() == 0) {
            Auth::guard('web')->logout();
            $redirectTo = $request->fullUrl();
            $request->session()->put('redirect_to', $redirectTo);
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        return view('documents.index', [
            'documents' => $documents,
            'documentType' => $docType,
            'filters' => [
                'keyword' => $request['keyword'],
                'sort' => $sortData->sort,
            ],
        ]);
    }

    public function show(Document $document): View
    {
        // $user = Auth::user();
        $user = User::find(1);

        try {
            $document = $this->documentService->get($document->id, $user);
        } catch (AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('documents.show', [
            'document' => $document,
        ]);
    }
}
