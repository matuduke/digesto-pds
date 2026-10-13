<?php

namespace App\Data;

use App\Data\PagingData;
use Carbon\Carbon;
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

    #[Property(property: 'date_from', type: 'string', format: 'date', example: '2026-04-15')]
    public ?string $dateFrom = null;

    #[Property(property: 'date_to', type: 'string', format: 'date', example: '2026-04-25')]
    public ?string $dateTo = null;

    #[Property(property: 'keyword', type: 'string', example: 'Seguridad Portuaria')]
    public ?string $keyword = null;

    #[Property(property: 'number', type: 'string', example: 'AD00001')]
    public ?string $number = null;

    #[Property(property: 'status', type: 'string', example: 'publicado')]
    public ?string $status = null;
}
