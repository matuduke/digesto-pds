<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Document Type
 *
 * @property-read int $id
 * @property string $name
 * @property string $description
 */
#[Fillable(
    ['name', 'description']
)]
class DocumentType extends Model
{
    use HasFactory, SoftDeletes;

    /** --------------------------------------------------
     * Model constants
     * -------------------------------------------------- */
    public const TABLE = 'document_types';

    public const ID = 'id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
