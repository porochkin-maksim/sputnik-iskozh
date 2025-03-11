<?php declare(strict_types=1);

namespace Core\Domains\Access\Models;

use App\Models\Access\Role;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Illuminate\Support\Str;

class RoleComparator extends AbstractComparatorDTO
{
    public const TITLE_NAME        = 'Название';
    public const TITLE_PERMISSIONS = 'Разрешения';

    protected const KEYS_TO_TITLES = [
        Role::NAME    => self::TITLE_NAME,
        'permissions' => self::TITLE_PERMISSIONS,
    ];

    public function __construct(RoleDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $groupedPermissions = [];
        foreach ($entity->getPermissions() as $permission) {
            $groupedPermissions[$permission->sectionName()][] = Str::lower($permission->name());
        }

        foreach ($groupedPermissions as $key => $value) {
            $groupedPermissions[$key] = sprintf("%s: %s", $key, implode(', ', $value));
        }

        $this->expandedProperties = [
            'permissions' => implode('; ', $groupedPermissions),
        ];
    }
}
