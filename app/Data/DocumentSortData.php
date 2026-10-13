<?php

namespace App\Data;

use App\Enums\SortDirection;
use App\Models\Document;

/**
 * Shipment item sorting.
 */
class DocumentSortData extends SortData
{
    /**
     * Constructor.
     */
    public function __construct(
        ?string $sort = null,
    ) {
        parent::__construct(
            $sort,
            column: Document::TABLE.'.'.Document::PUBLISH_DATE,
            direction: SortDirection::DESC->value
        );
    }
}
