<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * File
 *
 * @property-read int $id
 * @property int $document_id
 * @property string $name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 */
class File extends Model
{
    use HasFactory, SoftDeletes;

    /** --------------------------------------------------
     * Model constants
     * -------------------------------------------------- */
    public const TABLE = 'files';

    public const ID = 'id';
    public const DOCUMENT_ID = 'document_id';
    public const NAME = 'name';
    public const PATH = 'path';
    public const MIME_TYPE = 'mime_type';
    public const SIZE = 'size';

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
