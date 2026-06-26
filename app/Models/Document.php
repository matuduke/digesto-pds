<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

/**
 * Document
 *
 * @property-read int $id
 * @property int $document_type_id
 * @property int $user_id
 * @property string $title
 * @property string $number
 * @property Carbon $published_at
 * @property string $status
 * @property string $content
 */
#[Fillable(
    ['document_type_id', 'title', 'number', 'published_at', 'status', 'content']
)]
class Document extends Model
{
    use HasFactory, SoftDeletes;

    /** --------------------------------------------------
     * Model constants
     * -------------------------------------------------- */
    public const TABLE = 'documents';

    public const ID = 'id';
    public const DOCUMENT_TYPE_ID = 'document_type_id';
    public const USER_ID = 'user_id';
    public const TITLE = 'title';
    public const NUMBER = 'number';
    public const PUBLISHED_AT = 'published_at';
    public const STATUS = 'status';
    public const CONTENT = 'content';

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function authorizedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'authorized_users', 'document_id', 'user_id');
    }

    public function authorizedGroups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'authorized_groups', 'document_id', 'group_id');
    }

    public function scopeDocumentsICanView(Builder $builder, User $user)
    {
        return
            $builder
                ->leftJoin('authorized_users', function (JoinClause $join) use ($user) {
                    $join->on('authorized_users.user_id', '=', DB::raw($user->id))
                        ->on('authorized_users.document_id', '=', 'documents.id');
                })
                ->leftJoin('group_users', 'group_users.user_id', '=', DB::raw($user->id))
                ->leftJoin('authorized_groups', function(JoinClause $join) {
                    return $join->on('authorized_groups.group_id', '=', 'group_users.group_id')
                    ->on('authorized_groups.document_id', '=', 'documents.id');
                })
                ->where(function($query) {
                    return $query->whereNotNull('authorized_users.user_id')
                        ->orWhereNotNull('authorized_groups.group_id');
                });
    }
}
