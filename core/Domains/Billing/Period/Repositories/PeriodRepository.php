<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Repositories;

use App\Models\Billing\Period;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Billing\Period\Factories\PeriodFactory;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Period\Responses\PeriodSearchResponse;

class PeriodRepository
{
    use RepositoryTrait;

    private const string TABLE = Period::TABLE;

    public function __construct(
        private readonly PeriodFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Period::class;
    }

    public function search(SearcherInterface $searcher): PeriodSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new PeriodCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new PeriodSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?PeriodDTO
    {
        /** @var null|Period $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(PeriodDTO $dto): PeriodDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }
}
