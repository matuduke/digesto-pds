<?php

namespace App\Data;

use App\Enums\SortDirection;
use Illuminate\Support\Str;
use OpenApi\Attributes\Schema;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[Schema('SortData')]
#[MapName(SnakeCaseMapper::class)]
class SortData extends Data
{
    public function __construct(
        public ?string $sort = null,
        private ?array $columnsMapping = null,
        private ?string $column = null,
        private ?string $direction = null,
    ) {
        $this->setOptions($column, $direction);
    }

    /**
     * Set options with column and direction.
     *
     * @param string $column
     * @param string|null $direction
     */
    private function setOptions(string $column, ?string $direction): void
    {
        $sort = $this->sort;

        if (empty($sort)) {
            $this->column = $column;
            $this->direction = $direction;

            return;
        }

        $first = Str::substr($sort, 0, 1);

        if ($first === '-') {
            $column = Str::substr($sort, 1);
            $direction = SortDirection::DESC->value;
        } else {
            $column = $sort;
            $direction = SortDirection::ASC->value;
        }

        $this->column = $this->columnsMapping[$column] ?? $column;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}
