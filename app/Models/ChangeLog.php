<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
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
 * @property string $document_changes
 */
#[Fillable(
    ['document_id', 'user_id', 'document_changes', 'type']
)]
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

    public const TYPE = 'type';

    protected $table = self::TABLE;

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getParsedChanges(): string {
        if ($this->type == 'document-updated') {
            $fields = implode(', ', array_keys((array) json_decode($this->document_changes)));
            $fields = str_replace([
                'document_type_id',
                'number',
                'publish_date',
                'is_public',
                'content',
            ], [
                'Tipo de Normativa',
                'Número de norma',
                'Fecha de publicación',
                'Es de acceso público',
                'Contenido',
            ], $fields);

            return 'Se modificaron los campos: ' . $fields;
        } elseif ($this->type == 'groups-synched') {
            $changes = json_decode($this->document_changes);
            $message = '';
            if (count($changes->attached) > 0) {
                $groupsAttached = Group::whereIn('id', $changes->attached)->pluck('description')->toArray();
                if (count($changes->attached) == 1) {
                    $message .= 'Se agregó el grupo: ';
                } else {
                    $message .= 'Se agregaron los grupos: ';
                }
                $message .= join(', ', $groupsAttached);
            }
            if (count($changes->detached) > 0) {
                $groupsDetached = Group::whereIn('id', $changes->detached)->pluck('description')->toArray();
                if (strlen($message) > 0) {
                    $message .= '. Y s';
                } else {
                    $message .= 'S';
                }
                if (count($changes->detached) == 1) {
                    $message .= 'e removió el grupo: ';
                } else {
                    $message .= 'e removieron los grupos: ';
                }
                $message .= join(', ', $groupsDetached);
            }

            return $message;
        } elseif ($this->type == 'users-synched') {
            $changes = json_decode($this->document_changes);
            $message = '';
            if (count($changes->attached) > 0) {
                $usersAttached = User::whereIn('id', $changes->attached)->pluck('name')->toArray();
                if (count($changes->attached) == 1) {
                    $message .= 'Se agregó el usuario: ';
                } else {
                    $message .= 'Se agregaron los usuarios: ';
                }
                $message .= join(', ', $usersAttached);
            }
            if (count($changes->detached) > 0) {
                $usersDetached = User::whereIn('id', $changes->detached)->pluck('name')->toArray();
                if (strlen($message) > 0) {
                    $message .= '. Y s';
                } else {
                    $message .= 'S';
                }
                if (count($changes->detached) == 1) {
                    $message .= 'e removió el usuario: ';
                } else {
                    $message .= 'e removieron los usuarios: ';
                }
                $message .= join(', ', $usersDetached);
            }

            return $message;
        } else {
            return $this->document_changes;
        }
    }
}
