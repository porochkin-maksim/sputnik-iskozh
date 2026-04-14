<?php declare(strict_types=1);

namespace Core\Domains\Access\Services;

use App\Models\Access\Role;
use Core\Domains\Access\Collections\RoleCollection;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Factories\RoleFactory;
use Core\Domains\Access\Models\RoleDTO;
use Core\Domains\Access\Models\RoleSearcher;
use Core\Domains\Access\Repositories\RoleRepository;
use Core\Domains\Access\Responses\AccessSearchResponse;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Models\UserDTO;
use Illuminate\Support\Str;

readonly class RoleService
{
    public function __construct(
        private RoleFactory           $roleFactory,
        private RoleRepository        $roleRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(RoleDTO $role): RoleDTO
    {
        $model = $this->roleRepository->getById($role->getId());
        $before = $model ? $this->roleFactory->makeDtoFromObject($model) : new RoleDTO();

        $model   = $this->roleRepository->save($this->roleFactory->makeModelFromDto($role, $model));
        $current = $this->roleFactory->makeDtoFromObject($model);

        $this->logPermissionsChanges($before, $current, $role->getId() ? Event::UPDATE : Event::CREATE);

        return $current;
    }

    public function search(RoleSearcher $searcher): AccessSearchResponse
    {
        $response = $this->roleRepository->search($searcher);

        $result = new AccessSearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new RoleCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->roleFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(int $id): ?RoleDTO
    {
        $result = $this->roleRepository->getById($id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $user = $this->getById($id);

        if ( ! $user) {
            return false;
        }

        return $this->roleRepository->deleteById($id);
    }

    public function getByUserId(int|string|null $id): ?RoleDTO
    {
        $result = $this->roleFactory->makeForUserId($id);

        if ($result) {
            return $result;
        }

        if ( ! $id) {
            return null;
        }

        $result = $this->roleRepository->getByUserId((int) $id);

        return $result ? $this->roleFactory->makeDtoFromObject($result) : null;
    }

    public function all(): RoleCollection
    {
        return $this->search(new RoleSearcher())->getItems();
    }

    public function getEmailsByPermissions(PermissionEnum $permission): array
    {
        $searcher = new RoleSearcher();
        $searcher->setWithUsers();

        $roles = $this->search($searcher)->getItems()->filter(function (RoleDTO $role) use ($permission) {
            return $role->hasPermission($permission);
        });

        $userCollection = new UserCollection();
        foreach ($roles as $role) {
            $userCollection = $userCollection->merge($role->getUsers());
        }

        $emails = [];
        $userCollection->each(function (UserDTO $user) use (&$emails) {
            $emails[$user->getEmail()] = $user->getEmail();
        });

        return array_values($emails);
    }

    private function logPermissionsChanges(RoleDTO $before, RoleDTO $current, Event $event): void
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

    private function formatPermissions(RoleDTO $role): string
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
