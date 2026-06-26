<?php

namespace App\Data;

use App\Data\PagingData;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[Schema(title: 'GetDocumentsData')]
#[MapName(SnakeCaseMapper::class)]
class GetDocumentsData extends PagingData
{
    #[Property(property: 'document_type_id', type: 'integer', example: '12')]
    public ?int $documentTypeId = null;

    #[Property(property: 'date_from', type: 'date', example: '2026-04-15')]
    public ?int $dateFrom = null;

    #[Property(property: 'date_to', type: 'date', example: '2026-04-25')]
    public ?int $dateTo = null;

    #[Property(property: 'keyword', type: 'string', example: 'Seguridad Portuaria')]
    public ?string $keyword = null;

    #[Property(property: 'status', type: 'string', example: 'publicado')]
    public ?string $status = null;
}
