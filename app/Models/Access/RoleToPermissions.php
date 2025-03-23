<?php declare(strict_types=1);

namespace App\Models\Access;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $role
 * @property int $permission
 */
class RoleToPermissions extends Model
{
    public const TABLE = 'roles_to_permissions';

    public const ROLE       = 'role';
    public const PERMISSION = 'permission';

    protected $table = self::TABLE;

    protected $fillable = [
        self::ROLE,
        self::PERMISSION,
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, self::ROLE);
    }
}
