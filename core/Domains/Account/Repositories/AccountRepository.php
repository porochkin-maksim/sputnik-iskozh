<?php declare(strict_types=1);

namespace Core\Domains\Account\Repositories;

use App\Models\Account\Account;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Responses\AccountSearchResponse;

class AccountRepository
{
    use RepositoryTrait;

    private const string TABLE = Account::TABLE;

    public function __construct(
        private readonly AccountFactory          $accountFactory,
        private readonly AccountToUserRepository $accountToUserRepository,
    )
    {
    }

    protected function modelClass(): string
    {
        return Account::class;
    }

    public function search(SearcherInterface $searcher): AccountSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new AccountCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->accountFactory->makeDtoFromObject($model));
        }

        $result = new AccountSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection->sortDefault())
        ;

        return $result;
    }

    public function getById(?int $id): ?AccountDTO
    {
        /** @var ?Account $result */
        $result = $this->getModelById($id);

        return $result ? $this->accountFactory->makeDtoFromObject($result) : null;
    }

    public function getByIds(array $ids): AccountCollection
    {
        return $this->search(new AccountSearcher()->setIds($ids))->getItems();
    }

    public function save(AccountDTO $dto): AccountDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->accountFactory->makeModelFromDto($dto, $model);
        $model->save();

        if ($dto->hasUsers()) {
            $model->users()->sync($dto->getUsers()->getIds());
        }

        return $this->accountFactory->makeDtoFromObject($model);
    }

    public function getByUserId(int $id): AccountCollection
    {
        $ids = $this->accountToUserRepository->getAccountsIdsByUserId($id);
        if ( ! $ids) {
            return new AccountCollection();
        }

        return $this->search(AccountSearcher::make()
            ->setIds($ids)
            ->setWithUsers(),
        )->getItems();
    }
}
