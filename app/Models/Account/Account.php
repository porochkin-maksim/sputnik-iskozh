<?php declare(strict_types=1);

namespace App\Models\Account;

use App\Models\Billing\Invoice;
use App\Models\Interfaces\CastsInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?string $number
 * @property ?int    $size
 * @property ?float  $balance
 * @property ?bool   $is_verified
 * @property ?int    $primary_user_id
 * @property ?bool   $is_member
 * @property ?bool   $is_manager
 * @property ?User[] $users
 */
class Account extends Model implements CastsInterface
{
    use SoftDeletes;

    public const TABLE = 'accounts';

    protected $table = self::TABLE;

    public const ID              = 'id';
    public const NUMBER          = 'number';
    public const SIZE            = 'size';
    public const BALANCE         = 'balance';
    public const IS_VERIFIED     = 'is_verified';
    public const PRIMARY_USER_ID = 'primary_user_id';
    public const IS_MEMBER       = 'is_member';
    public const IS_MANAGER      = 'is_manager';

    public const USERS = 'users';

    protected $guarded = [];

    protected $casts = [
        self::PRIMARY_USER_ID => self::CAST_INTEGER,
        self::SIZE            => self::CAST_INTEGER,
        self::IS_VERIFIED     => self::CAST_BOOLEAN,
        self::IS_MEMBER       => self::CAST_BOOLEAN,
        self::IS_MANAGER      => self::CAST_BOOLEAN,
        self::BALANCE         => self::CAST_FLOAT,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            AccountToUser::TABLE,
            AccountToUser::ACCOUNT,
            AccountToUser::USER,
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class,
            Invoice::ACCOUNT_ID,
            self::ID,
        );
    }
}
