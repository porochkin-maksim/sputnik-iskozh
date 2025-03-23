<?php

namespace App\Models\Access;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
class Role extends Model
{
    public const TABLE = 'roles';

    protected $table = self::TABLE;

    public const ID   = 'id';
    public const NAME = 'name';

    public const USERS       = 'users';
    public const PERMISSIONS = 'permissions';

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
