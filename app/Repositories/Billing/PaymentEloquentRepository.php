<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Account\Account;
use App\Models\Billing\Payment;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentRepositoryInterface;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentSearchResponse;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use Illuminate\Database\Eloquent\Builder;
use ReturnTypeWillChange;

class PaymentEloquentRepository implements PaymentRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly PaymentEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return Payment::class;
    }

    protected function getTable(): string
    {
        return Payment::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new PaymentCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return PaymentSearchResponse
     */
    protected function getEmptySearchResponse(): PaymentSearchResponse
    {
        return new PaymentSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return PaymentSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new PaymentSearcher();
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
