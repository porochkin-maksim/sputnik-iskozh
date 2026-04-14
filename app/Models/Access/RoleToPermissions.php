<?php declare(strict_types=1);

namespace App\Models\Access;

use App\Models\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $role
 * @property int $permission
 */
class RoleToPermissions extends AbstractModel
{
    public const string TABLE = 'roles_to_permissions';

    public const string ROLE       = 'role';
    public const string PERMISSION = 'permission';

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
