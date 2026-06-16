<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Transaction;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Transaction\TransactionCollection;
use Core\Domains\Billing\Transaction\TransactionEntity;
use Core\Domains\Billing\Transaction\TransactionRepositoryInterface;
use Core\Domains\Billing\Transaction\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;

class TransactionEloquentRepository implements TransactionRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly TransactionEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Transaction::class,
            table              : Transaction::TABLE,
            collectionClass    : TransactionCollection::class,
            searchResponseClass: TransactionSearchResponse::class,
            searcherClass      : TransactionSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): TransactionSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?TransactionEntity
    {
        /** @var Transaction|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function save(TransactionEntity $transaction): TransactionEntity
    {
        /** @var Transaction|null $model */
        $model = $this->getModelById($transaction->getId());
        /** @var Transaction $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($transaction, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function deleteByClaimIds(array $claimIds): void
    {
        Transaction::whereIn(Transaction::CLAIM_ID, $claimIds)->delete();
    }

    public function deleteByPaymentIds(array $paymentIds): void
    {
        Transaction::whereIn(Transaction::PAYMENT_ID, $paymentIds)->delete();
    }
}
