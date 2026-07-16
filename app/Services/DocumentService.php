<?php

namespace App\Services;

use App\Data\CreateDocumentData;
use App\Data\DocumentSortData;
use App\Data\GetDocumentsData;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DocumentService
{
    public function __construct() {}

    public function list(GetDocumentsData $data, DocumentSortData $sortData, ?User $user = null): LengthAwarePaginator
    {
        $documents = Document::query()
            ->select(DB::raw($this->getSelectFields()))
            ->when($data->keyword, function ($query) use ($data) {
                return $query->where(function($query) use ($data) {
                    $query->where(Document::TABLE.'.'.Document::NUMBER, 'like', '%'.$data->keyword.'%')
                        ->orWhere(Document::TABLE.'.'.Document::CONTENT, 'like', '%'.$data->keyword.'%');
                });
            })
            ->when($data->documentTypeId, function ($query) use ($data) {
                return $query->where(Document::TABLE.'.'.Document::DOCUMENT_TYPE_ID, $data->documentTypeId);
            })
            ->when($data->dateFrom, function ($query) use ($data) {
                return $query->where(Document::TABLE.'.'.Document::CREATED_AT, '>=', $data->dateFrom);
            })
            ->when($data->dateTo, function ($query) use ($data) {
                return $query->where(Document::TABLE.'.'.Document::CREATED_AT, '<=', $data->dateTo);
            })
            ->when($data->status, function ($query) use ($data) {
                return $query->where(Document::TABLE.'.'.Document::STATUS, $data->status);
            })
            ->join(DocumentType::TABLE, DocumentType::TABLE.'.'.DocumentType::ID, '=', Document::DOCUMENT_TYPE_ID)
            ->orderBy($this->mapSortColumn($sortData->getColumn()), $sortData->getDirection());

        // Admin users doesn't have restrictions
        if ($user) {
            if(!$user->isAdmin()) {
                $documents = $documents->documentsICanView($user);
            }
        } else {
            $documents->where(Document::IS_PUBLIC, true);
        }

        DB::listen(function ($documents) {
            Log::info($documents->sql, ['Bindings' => $documents->bindings, 'Time' => $documents->time]);
        });

        return $documents
            ->paginate($data->perPage)
            ->withQueryString();
    }

    private function mapSortColumn(string $column): string
    {
        if ($column == 'document_type_id') {
            return DocumentType::TABLE.'.'.DocumentType::DESCRIPTION;
        } elseif ($column == 'created_at') {
            return Document::TABLE.'.'.Document::CREATED_AT;
        } else {
            return $column;
        }
    }

    private function getSelectFields(): string
    {
        return 'distinct '.Document::TABLE.'.' . Document::ID . ', '.Document::TABLE.'.*, '.
            DocumentType::TABLE.'.'.DocumentType::NAME.','.
            DocumentType::TABLE.'.'.DocumentType::DESCRIPTION;
    }

    /**
     * Return the requested document or null if not found or the user is not allowed
     *
     * @throws AccessDeniedHttpException
     */
    public function get(int $documentId, User $user): Document
    {
        if ($this->userHasAccess($documentId, $user)) {
            $document = Document::find($documentId);
            if ($document) {
                $document->load(['documentType', 'user', 'files', 'authorizedUsers', 'authorizedGroups']);

                return $document;
            } else {
                throw new UnprocessableEntityHttpException('La norma que desea ver no existe');
            }
        } else {
            throw new AccessDeniedHttpException('No tiene permisos para modificar a esta norma');
        }
    }

    public function store(CreateDocumentData $documentData, User $user): Document
    {
        $document = Document::create($documentData->toArray() + ['user_id' => $user->id]);

        if ($document) {
            // Authorize users and groups
            $authorizedUsers = explode(',', $documentData->authorizedUserIds);
            $document->authorizedUsers()->sync($authorizedUsers);

            $authorizedGroups = explode(',', $documentData->authorizedGroupIds);
            $document->authorizedGroups()->sync($authorizedGroups);
        }

        return $document;
    }

    public function update(Document $document, CreateDocumentData $documentData): bool
    {
        if ($document) {
            // Authorize users and groups
            $authorizedUsers = explode(',', $documentData->authorizedUserIds);
            $document->authorizedUsers()->sync($authorizedUsers);

            $authorizedGroups = explode(',', $documentData->authorizedGroupIds);
            $document->authorizedGroups()->sync($authorizedGroups);
        }

        return $document->update($documentData->toArray());
    }

    public function destroy(Document $document): bool
    {
        return $document->delete();
    }

    public function userHasAccess(int $documentId, User $user): bool
    {
        if (! $user->isAdmin()) {
            $document = Document::where(Document::TABLE.'.'.Document::ID, $documentId)
                ->documentsICanView($user)
                ->first();

            return isset($document);
        } else {
            return true;
        }
    }
}
