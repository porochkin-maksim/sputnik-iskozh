<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Account\Account;
use App\Models\Billing\Payment;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentRepositoryInterface;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;

class PaymentEloquentRepository implements PaymentRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly PaymentEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Payment::class,
            table              : Payment::TABLE,
            collectionClass    : PaymentCollection::class,
            searchResponseClass: PaymentSearchResponse::class,
            searcherClass      : PaymentSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): PaymentSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id): ?PaymentEntity
    {
        /** @var Payment|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): PaymentSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function save(PaymentEntity $payment): PaymentEntity
    {
        /** @var Payment|null $model */
        $model = $this->getModelById($payment->getId());
        /** @var Payment $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($payment, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    protected function getQuery(Builder $query): Builder
    {
        return $query->select(Payment::TABLE . '.*')
            ->leftJoin(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, Payment::TABLE . '.' . Payment::ACCOUNT_ID)
            ->selectSub(Account::TABLE . '.' . Account::NUMBER, 'account_number');
    }
}
