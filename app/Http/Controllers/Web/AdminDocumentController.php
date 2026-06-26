<?php

namespace App\Http\Controllers\Web;

use App\Data\CreateDocumentData;
use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Group;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminDocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) { }

    public function index(Request $request, GetDocumentsData $data, DocumentSortData $sortData): View
    {
        $documentTypeId = intval($request['document_type_id'], 10);

        // $user = Auth::user();
        $user = User::find(1);
        $documents = $this->documentService->list($data, $sortData, $user);
        $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::DESCRIPTION)
            ->pluck(DocumentType::DESCRIPTION, DocumentType::ID);
        $statuses = [
            'borrador' => 'Borrador',
            'publicado' => 'Publicado',
        ];

        return view('documents.index', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'statuses' => $statuses,
            'filters' => [
                'keyword' => $request['keyword'],
                'document_type_id' => $documentTypeId,
                'status' => $request['status'],
                'sort' => $sortData->sort,
            ],
        ]);
    }

    public function new(): View
    {
        $documentTypes = DocumentType::query()
            ->orderBy(DocumentType::NAME)
            ->pluck('description', 'id');

        $users = User::query()
            ->orderBy(User::NAME)
            ->get();

        $groups = Group::query()
            ->orderBy(Group::NAME)
            ->get();

        return view('documents.form', [
            'document' => new Document(),
            'documentTypes' => $documentTypes,
            'users' => $users,
            'groups' => $groups,
        ]);
    }

    public function show(Document $document): View
    {
        // $user = Auth::user();
        $user = User::find(1);

        try {
            $document = $this->documentService->get($document->id, $user);
        } catch(AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('documents.show', [
            'document' => $document,
        ]);
    }

    public function update(CreateDocumentData $data): View
    {
        // $user = Auth::user();
        $user = User::find(1);
        try {
            if ($data->id) {
                $document = $this->documentService->get($data->id, $user);
            } else {
                $document = new Document();
            }
            dd($data->toArray());
            $document->fill($data->toArray());
            $document->save();
        } catch(AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

        return view('documents.form', [
            'document' => $document,
        ]);
    }

    public function edit(Document $document): View
    {
        // $user = Auth::user();
        $user = User::find(1);

        try {
            $document = $this->documentService->get($document->id, $user);
        } catch(AccessDeniedHttpException $exception) {
            abort(403, $exception->getMessage());
        }

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

        return view('documents.form', [
            'document' => $document,
            'documentTypes' => $documentTypes,
            'statuses' => $statuses,
            'users' => $users,
            'groups' => $groups,
        ]);
    }
}
