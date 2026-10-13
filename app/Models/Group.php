<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * UserGroup
 *
 * @property-read int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(
    ['description']
)]
class Group extends Model
{
    use HasFactory, SoftDeletes;

    /** --------------------------------------------------
     * Model constants
     * -------------------------------------------------- */
    public const TABLE = 'groups';

    public const ID = 'id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';

    public const ADMINISTRATORS_GROUP = 'administradores_del_sistema';
    public const DOCUMENT_CREATORS_GROUP = 'creadores_de_reglamentaciones';
    public const DOCUMENT_APPROVERS_GROUP = 'aprobadores_de_reglamentaciones';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_users');
    }

    public function authorizedDocuments(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'authorized_groups', 'group_id', 'document_id');
    }
}
