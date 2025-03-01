<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Repositories;

use App\Models\Billing\Service;
use Core\Db\RepositoryTrait;
use Core\Domains\Billing\Service\Collections\ServiceCollection;

class ServiceRepository
{
    private const TABLE = Service::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Service::class;
    }

    public function getById(?int $id): ?Service
    {
        /** @var ?Service $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): ServiceCollection
    {
        return new ServiceCollection($this->traitGetByIds($ids));
    }

    public function save(Service $service): Service
    {
        $service->save();

        return $service;
    }
}
