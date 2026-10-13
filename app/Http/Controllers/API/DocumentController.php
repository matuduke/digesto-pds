<?php

namespace App\Http\Controllers\API;

use App\Data\CreateDocumentData;
use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Enums\DocumentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\DocumentListResource;
use App\Http\Resources\GenericResourceCollection;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\QueryParameter;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) { }

    #[Get(
        path: '/api/documents',
        operationId: 'ListDocuments',
        summary: "List documents",
        security: [['cookieSecret' => []]],
        requestBody: new RequestBody(ref: "#/components/requestBodies/CreateDocumentData"),
        tags: ['Documents'],
        parameters: [
            new QueryParameter(
                name: 'keyword',
                description: 'Search by words in title or content',
                example: 'seguridad',
            ),
            new QueryParameter(
                name: 'type_id',
                description: 'Search by document type',
                example: '12',
            ),
            new QueryParameter(
                name: 'date_from',
                description: 'Search by published date',
                example: '2023-08-30',
            ),
            new QueryParameter(
                name: 'date_to',
                description: 'Search by published date',
                example: '2023-09-15',
            ),
        ],
        responses: [
            new Response(response: 201, description: "List of documents",
                content: new JsonContent(ref: "#/components/schemas/DocumentResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function index(GetDocumentsData $data, DocumentSortData $sortData): GenericResourceCollection
    {
        $user = Auth::user();
        $data->status = DocumentStatus::PUBLISHED->value;
        return GenericResourceCollection::make($this->documentService->list($data, $sortData, $user), DocumentListResource::class);
    }

    #[Get(
        path: '/api/documents/{document}',
        operationId: 'GetDocuments',
        summary: "Get single document",
        security: [['cookieSecret' => []]],
        tags: ['Documents'],
        parameters: [
            new PathParameter(
                name: 'document',
                description: 'Document ID',
                example: '477',
            ),
        ],
        responses: [
            new Response(response: 201, description: "Document",
                content: new JsonContent(ref: "#/components/schemas/DocumentResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function show(Document $document): JsonResponse
    {
        $user = Auth::user();
        if ($this->documentService->userHasAccess($document->id, $user)) {
            return response()->json($document);
        } else {
            throw new AccessDeniedHttpException('No tiene permisos para modificar a este documento');
        }
    }

    #[Post(
        path: '/api/documents',
        operationId: 'CreateDocument',
        summary: "Create new document",
        security: [['cookieSecret' => []]],
        requestBody: new RequestBody(ref: "#/components/requestBodies/CreateDocumentData"),
        tags: ['Documents'],
        responses: [
            new Response(response: 201, description: "Document created",
                content: new JsonContent(ref: "#/components/schemas/DocumentResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function store(CreateDocumentData $documentData): DocumentResource
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $document = $this->documentService->store($documentData, $user);
            return new DocumentResource($document);
        } else {
            throw new AccessDeniedHttpException('No tiene permisos para crear nuevos documentos');
        }

    }

    #[Put(
        path: '/api/documents/{document}',
        operationId: 'UpdateDocument',
        summary: "Update an existing document",
        security: [['cookieSecret' => []]],
        requestBody: new RequestBody(ref: "#/components/requestBodies/CreateDocumentData"),
        tags: ['Documents'],
        responses: [
            new Response(response: 201, description: "Document updated",
                content: new JsonContent(ref: "#/components/schemas/DocumentResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function update(CreateDocumentData $documentData, Document $document): DocumentResource
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            if ($this->documentService->update($document, $documentData)) {
                return new DocumentResource($document);
            } else {
                throw new UnprocessableEntityHttpException('Se produjo un error al actualizar el documento');
            }
        } else {
            throw new AccessDeniedHttpException('No tiene permisos para modificar a este documento');
        }
    }

    #[Delete(
        path: '/api/documents/{document}',
        operationId: 'DeleteDocument',
        summary: "Delete a document",
        security: [['cookieSecret' => []]],
        tags: ['Documents'],
        responses: [
            new Response(response: 204, description: "Document deleted",
                content: new JsonContent(ref: "#/components/schemas/DocumentResource")),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(ref: '#/components/responses/UnprocessableEntity', response: 422),
        ]
    )]
    public function destroy(Document $document): JsonResponse
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $document->delete();
            return response()->json(status: 204);
        } else {
            throw new AccessDeniedHttpException('No tiene permisos para eliminar este documento');
        }
    }
}
