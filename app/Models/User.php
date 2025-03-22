<?php

namespace App\Models;

use App\Models\Access\Role;
use App\Models\Access\RoleToUser;
use App\Models\Account\Account;
use App\Models\Account\AccountToUser;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\User\Notifications\InviteNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int     id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  email
 * @property ?string first_name
 * @property ?string middle_name
 * @property ?string last_name
 * @property string  password
 * @property bool    remember_token
 * @property string  email_verified_at
 * @property int     telegram_id
 */
class User extends Authenticatable implements CastsInterface, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const TABLE = 'users';

    public const ID                = 'id';
    public const EMAIL             = 'email';
    public const FIRST_NAME        = 'first_name';
    public const MIDDLE_NAME       = 'middle_name';
    public const LAST_NAME         = 'last_name';
    public const PASSWORD          = 'password';
    public const REMEMBER_TOKEN    = 'remember_token';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const TELEGRAM_ID       = 'telegram_id';

    public const ACCOUNTS = 'accounts';
    public const ROLES    = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::EMAIL,
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
        );
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

    public function sendInviteNotification(string $email): void
    {
        $this->notify(new InviteNotification($email));
    }
}
