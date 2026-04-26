<?php declare(strict_types=1);

namespace Core\Domains\Access;

use App\Models\Access\Role;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Illuminate\Support\Str;

readonly class RoleService
{
    public function __construct(
        private RoleFactory $roleFactory,
        private RoleRepositoryInterface $roleRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(RoleEntity $role): RoleEntity
    {
        $before = $role->getId()
            ? $this->roleRepository->getById($role->getId()) ?? $this->roleFactory->makeDefault()
            : $this->roleFactory->makeDefault();
        $current = $this->roleRepository->save($role);

        $this->logPermissionsChanges($before, $current, $role->getId() ? Event::UPDATE : Event::CREATE);

        return $current;
    }

    public function search(RoleSearcher $searcher): RoleSearchResponse
    {
        return $this->roleRepository->search($searcher);
    }

    public function getById(int $id): ?RoleEntity
    {
        return $this->roleRepository->getById($id);
    }

    public function deleteById(int $id): bool
    {
        if ( ! $this->getById($id)) {
            return false;
        }

        return $this->roleRepository->deleteById($id);
    }

    public function getByUserId(int|string|null $id): ?RoleEntity
    {
        $result = $this->roleFactory->makeForUserId($id);

        if ($result) {
            return $result;
        }

        if ( ! $id) {
            return null;
        }

        return $this->roleRepository->getByUserId((int) $id);
    }

    public function all(): RoleCollection
    {
        return $this->search(new RoleSearcher())->getItems();
    }

    public function getEmailsByPermissions(PermissionEnum $permission): array
    {
        $searcher = new RoleSearcher();
        $searcher->setWithUsers();

        $roles = $this->search($searcher)->getItems()->filter(function (RoleEntity $role) use ($permission) {
            return $role->hasPermission($permission);
        });

        $userCollection = new UserCollection();
        foreach ($roles as $role) {
            $userCollection = $userCollection->merge($role->getUsers());
        }

        $emails = [];
        $userCollection->each(function (UserEntity $user) use (&$emails) {
            $emails[$user->getEmail()] = $user->getEmail();
        });

        return array_values($emails);
    }

    private function logPermissionsChanges(RoleEntity $before, RoleEntity $current, Event $event): void
    {
        $oldValue = $this->formatPermissions($before);
        $newValue = $this->formatPermissions($current);

        if ($oldValue === $newValue) {
            return;
        }

        $changes = new ChangesCollection();
        $changes->add(new Changes(
            Role::TITLE_PERMISSIONS,
            $oldValue,
            $newValue,
        ));

        $this->historyChangesService->logChanges(
            $event,
            HistoryType::ROLE,
            $changes,
            $current->getId(),
        );
    }

    private function formatPermissions(RoleEntity $role): string
    {
        $groupedPermissions = [];
        foreach ($role->getPermissions() as $permission) {
            $groupedPermissions[$permission->sectionName()][] = Str::lower($permission->name());
        }

        foreach ($groupedPermissions as $key => $value) {
            $groupedPermissions[$key] = sprintf("%s: %s", $key, implode(', ', $value));
        }

        return implode('; ', $groupedPermissions);
    }
}
