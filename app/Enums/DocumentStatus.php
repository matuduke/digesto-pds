<?php

namespace App\Enums;

/**
 * Document statuses
 */
enum DocumentStatus: string
{
    use EnumToArray;

    case DRAFT = 'borrador';
    case PUBLISHED = 'publicado';
}
