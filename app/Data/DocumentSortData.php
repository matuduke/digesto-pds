<?php

namespace App\Data;

use App\Data\SortData;
use App\Enums\SortDirection;
use App\Models\Document;

/**
 * Shipment item sorting.
 */
class DocumentSortData extends SortData
{
    /**
     * Constructor.
     *
     * @param string|null $sort
     */
    public function __construct(
        ?string $sort = null,
    ) {
        parent::__construct(
            $sort,
            column: Document::PUBLISHED_AT,
            direction: SortDirection::DESC->value
        );
    }
}
