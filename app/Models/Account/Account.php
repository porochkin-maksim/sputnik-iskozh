<?php declare(strict_types=1);

namespace App\Models\Account;

use App\Models\AbstractModel;
use App\Models\Billing\Invoice;
use App\Models\Infra\ExData;
use App\Models\Interfaces\CastsInterface;
use App\Models\User;
use Carbon\Carbon;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * @property ?bool   $is_invoicing
 * @property ?string $sort_value
 * @property ?User[] $users
 */
class Account extends AbstractModel
{
    use SoftDeletes;

    public const string TABLE = 'accounts';

    public const string ID              = 'id';
    public const string NUMBER          = 'number';
    public const string SIZE            = 'size';
    public const string BALANCE         = 'balance';
    public const string IS_VERIFIED     = 'is_verified';
    public const string PRIMARY_USER_ID = 'primary_user_id';
    public const string IS_INVOICING    = 'is_invoicing';
    public const string SORT_VALUE      = 'sort_value';

    public const string USERS   = 'users';
    public const string EX_DATA = 'exData';

    protected $with = [self::EX_DATA];

    protected $casts = [
        self::PRIMARY_USER_ID => self::CAST_INTEGER,
        self::SIZE            => self::CAST_INTEGER,
        self::IS_VERIFIED     => self::CAST_BOOLEAN,
        self::IS_INVOICING    => self::CAST_BOOLEAN,
        self::BALANCE         => self::CAST_FLOAT,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            AccountToUser::TABLE,
            AccountToUser::ACCOUNT,
            AccountToUser::USER,
        )->withPivot(AccountToUser::FRACTION);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class,
            Invoice::ACCOUNT_ID,
            self::ID,
        );
    }

    public function exData(): HasOne
    {
        return $this->hasOne(
            ExData::class,
            ExData::REFERENCE_ID,
            self::ID,
        )->where(ExData::TYPE, ExDataTypeEnum::ACCOUNT);
    }
}
