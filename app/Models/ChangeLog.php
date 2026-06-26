<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Change Log
 *
 * @property-read int $id
 * @property int $document_id
 * @property int $user_id
 * @property string $changes
 */
class ChangeLog extends Model
{
    use HasFactory, SoftDeletes;

    /** --------------------------------------------------
     * Model constants
     * -------------------------------------------------- */
    public const TABLE = 'change_log';

    public const ID = 'id';
    public const DOCUMENT_ID = 'document_id';
    public const USER_ID = 'user_id';
    public const CHANGES = 'changes';

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
