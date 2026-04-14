<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;
use App\Models\User;
use Carbon\Carbon;
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
class HistoryChanges extends AbstractModel
{
    public const string TABLE = 'history_changes';

    protected $table = self::TABLE;

    public const string ID             = 'id';
    public const string TYPE           = 'type';
    public const string REFERENCE_TYPE = 'reference_type';
    public const string USER_ID        = 'user_id';
    public const string PRIMARY_ID     = 'primary_id';
    public const string REFERENCE_ID   = 'reference_id';
    public const string DESCRIPTION    = 'description';
    public const string USER           = 'user';

    protected $guarded = [];

    protected $casts = [
        self::DESCRIPTION => self::CAST_JSON,
    ];
    protected $with  = [
        self::USER,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID, User::ID);
    }
}