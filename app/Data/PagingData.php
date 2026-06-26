<?php

namespace App\Data;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[Schema(
    title: 'PagingData',
    properties: [
        new Property('page', type: 'integer', example: 1),
        new Property('per_page', type: 'integer', example: 10),
    ]
)]
#[MapName(SnakeCaseMapper::class)]
class PagingData extends Data
{
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1)]
        public int $perPage = 10,
    ) {}
}
