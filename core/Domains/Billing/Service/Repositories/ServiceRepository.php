<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Repositories;

use App\Models\Billing\Service;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Billing\Service\Factories\ServiceFactory;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Billing\Service\Responses\ServiceSearchResponse;

class ServiceRepository
{

    use RepositoryTrait;

    private const string TABLE = Service::TABLE;

    public function __construct(
        private readonly ServiceFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Service::class;
    }

    public function search(SearcherInterface $searcher): ServiceSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new ServiceCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new ServiceSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?ServiceDTO
    {
        /** @var null|Service $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(ServiceDTO $dto): ServiceDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }
}
