<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Repositories;

use App\Models\Billing\Claim;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Factories\ClaimFactory;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\Responses\ClaimSearchResponse;

class ClaimRepository
{
    use RepositoryTrait;

    private const string TABLE = Claim::TABLE;

    public function __construct(
        private readonly ClaimFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Claim::class;
    }

    public function search(SearcherInterface $searcher): ClaimSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new ClaimCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new ClaimSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?ClaimDTO
    {
        /** @var null|Claim $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(ClaimDTO $dto): ClaimDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }
}
