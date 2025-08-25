<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Access\Role;
use App\Models\Access\RoleToUser;
use App\Models\Account\Account;
use App\Models\Account\AccountToUser;
use App\Models\Infra\ExData;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\User\Notifications\InviteNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $email
 * @property ?string $phone
 * @property ?string $first_name
 * @property ?string $middle_name
 * @property ?string $last_name
 * @property string  $password
 * @property bool    $remember_token
 * @property string  $email_verified_at
 * @property int     $telegram_id
 */
class User extends Authenticatable implements CastsInterface, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const string TABLE = 'users';

    public const string ID                = 'id';
    public const string EMAIL             = 'email';
    public const string PHONE             = 'phone';
    public const string FIRST_NAME        = 'first_name';
    public const string MIDDLE_NAME       = 'middle_name';
    public const string LAST_NAME         = 'last_name';
    public const string PASSWORD          = 'password';
    public const string REMEMBER_TOKEN    = 'remember_token';
    public const string EMAIL_VERIFIED_AT = 'email_verified_at';
    public const string TELEGRAM_ID       = 'telegram_id';

    public const string ACCOUNTS = 'accounts';
    public const string ROLES    = 'roles';
    public const string EX_DATA  = 'exData';

    protected $with = [
        self::EX_DATA,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::EMAIL,
        self::PHONE,
        self::PASSWORD,
        self::FIRST_NAME,
        self::MIDDLE_NAME,
        self::LAST_NAME,
        self::TELEGRAM_ID,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => self::CAST_DATETIME,
        self::PASSWORD          => self::CAST_HASHED,
    ];

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(
            Account::class,
            AccountToUser::TABLE,
            AccountToUser::USER,
            AccountToUser::ACCOUNT,
        )->withPivot(AccountToUser::FRACTION);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            RoleToUser::TABLE,
            RoleToUser::USER,
            RoleToUser::ROLE,
        );
    }

    public function exData(): HasOne
    {
        return $this->hasOne(
            ExData::class,
            ExData::REFERENCE_ID,
            self::ID,
        )->where(ExData::TYPE, ExDataTypeEnum::USER);
    }

    public function sendInviteNotification(string $email): void
    {
        $this->notify(new InviteNotification($email));
    }
}
