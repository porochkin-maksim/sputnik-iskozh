<?php declare(strict_types=1);

namespace Core\Domains\Counter\Repositories;

use App\Models\Counter\Counter;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Counter\Collections\CounterCollection;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Responses\CounterSearchResponse;

class CounterRepository
{
    use RepositoryTrait;

    private const string TABLE = Counter::TABLE;

    public function __construct(
        private readonly CounterFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Counter::class;
    }

    public function search(SearcherInterface $searcher): CounterSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new CounterCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new CounterSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?CounterDTO
    {
        /** @var null|Counter $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(CounterDTO $dto): CounterDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }
}
