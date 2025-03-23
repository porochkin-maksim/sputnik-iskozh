<?php

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $type
 * @property int     $reference_type
 * @property int     $user_id
 * @property int     $primary_id
 * @property int     $reference_id
 * @property array   $description
 */
class HistoryChanges extends Model implements CastsInterface
{
    public const TABLE = 'history_changes';

    protected $table = self::TABLE;

    public const ID             = 'id';
    public const TYPE           = 'type';
    public const REFERENCE_TYPE = 'reference_type';
    public const USER_ID        = 'user_id';
    public const PRIMARY_ID     = 'primary_id';
    public const REFERENCE_ID   = 'reference_id';
    public const DESCRIPTION    = 'description';

    protected $guarded = [];

    protected $casts = [
        self::DESCRIPTION => self::CAST_JSON,
    ];

    public const USER = 'user';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID);
    }
}