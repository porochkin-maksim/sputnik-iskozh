<?php declare(strict_types=1);

namespace App\Models\Access;

use App\Models\AbstractModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property int[]   $permissions
 *
 * @property ?string $name
 */
class Role extends AbstractModel
{
    public const string TABLE = 'roles';

    public const string ID   = 'id';
    public const string NAME = 'name';

    public const string USERS       = 'users';
    public const string PERMISSIONS = 'permissions';

    public const string TITLE_NAME        = 'Название';
    public const string TITLE_PERMISSIONS = 'Разрешения';

    public const array PROPERTIES_TO_TITLES = [
        self::NAME        => self::TITLE_NAME,
        self::PERMISSIONS => self::TITLE_PERMISSIONS,
    ];

    protected $with       = [self::PERMISSIONS];
    public    $timestamps = false;
    protected $guarded    = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            RoleToUser::TABLE,
            RoleToUser::ROLE,
            RoleToUser::USER,
        );
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RoleToPermissions::class, RoleToPermissions::ROLE, self::ID);
    }
}
