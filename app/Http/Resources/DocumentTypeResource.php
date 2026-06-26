<?php

namespace App\Http\Resources;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    title: 'DocumentTypeResource',
    properties: [
        new Property(property: DocumentType::ID, type: 'integer', example: 2),
        new Property(property: DocumentType::NAME, type: 'string', example: 'Normativa'),
        new Property(property: DocumentType::DESCRIPTION, type: 'string', example: 'Documentos de normativa interna'),
    ]
)]
/**
 * @property-read DocumentType $resource
 */
class DocumentTypeResource extends GenericResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            DocumentType::ID => $this->resource->id,
            DocumentType::NAME => $this->resource->name,
            DocumentType::DESCRIPTION => $this->resource->description,
        ];
    }
}
