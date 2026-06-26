<?php

namespace App\Http\Resources;

use App\Models\Document;
use Illuminate\Http\Request;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'DocumentListResource',
    properties: [
        new Property(property: Document::ID, type: 'integer', example: 8),
        new Property(property: Document::DOCUMENT_TYPE_ID, type: 'integer', example: 2),
        new Property(property: Document::USER_ID, type: 'integer', example: 4),
        new Property(property: Document::TITLE, type: 'string', example: 'Normativa Portuaria 2026'),
        new Property(property: Document::NUMBER, type: 'string', example: 'DOC-2026-001'),
        new Property(property: Document::PUBLISHED_AT, type: 'string', format: 'date', example: '2026-06-20'),
        new Property(property: Document::STATUS, type: 'string', example: 'borrador'),
    ]
)]
/**
 * @property-read Document $resource
 */
class DocumentListResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            Document::ID => $this->resource->id,
            Document::TITLE => $this->resource->title,
            Document::NUMBER => $this->resource->number,
            Document::PUBLISHED_AT => $this->resource->published_at,
            Document::STATUS => $this->resource->status,
            'user' => $this->resource->user,
            'document_type' => $this->resource->documentType,
        ];
    }
}
