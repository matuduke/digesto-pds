<?php

/**
 * Format file size in human-readable format
 */
function formatFileSize(int|float $bytes): string
{
    if ($bytes < 1024) {
        return $bytes.' B';
    }
    if ($bytes < 1024 * 1024) {
        return number_format($bytes / 1024, 1, '.', '').' KB';
    }

    return number_format($bytes / (1024 * 1024), 1, '.', '').' MB';
}
