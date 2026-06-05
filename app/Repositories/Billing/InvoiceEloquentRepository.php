<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceRepositoryInterface;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;

class InvoiceEloquentRepository implements InvoiceRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly InvoiceEloquentMapper $mapper,
    )
    {
    }

    protected function repositoryConfig(): RepositoryConfig
    {
        return new RepositoryConfig(
            modelClass         : Invoice::class,
            table              : Invoice::TABLE,
            collectionClass    : InvoiceCollection::class,
            searchResponseClass: InvoiceSearchResponse::class,
            searcherClass      : InvoiceSearcher::class,
        );
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    protected function adaptFieldName(string $field): string
    {
        if ($field === 'account_sort') {
            return $field;
        }

        return sprintf('%s.%s', $this->getTable(), $field);
    }

    protected function getQuery(Builder $query): Builder
    {
        $query->select(Invoice::TABLE . '.*')
            ->join(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, Invoice::TABLE . '.' . Invoice::ACCOUNT_ID)
            ->selectSub(Account::TABLE . '.' . Account::SORT_VALUE, 'account_sort');

        return $query;
    }

    public function search(SearcherInterface $searcher): InvoiceSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function save(InvoiceEntity $invoice): InvoiceEntity
    {
        /** @var Invoice|null $model */
        $model = $this->getModelById($invoice->getId());
        /** @var Invoice $model */
        $model = $this->mapper->makeRepositoryDataFromEntity($invoice, $model);
        $model->save();

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getById(?int $id): ?InvoiceEntity
    {
        /** @var Invoice|null $model */
        $model = $this->getModelById($id);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): InvoiceSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function getAccountIdsWithoutRegularInvoice(int $periodId): array
    {
        return Account::query()
            ->select([Account::TABLE . '.' . Account::ID])
            ->whereDoesntHave('invoices', function ($query) use ($periodId) {
                $query->where(Invoice::PERIOD_ID, $periodId)
                    ->where(Invoice::TYPE, InvoiceTypeEnum::REGULAR->value);
            })
            ->where(Account::TABLE . '.' . Account::ID, '!=', AccountIdEnum::SNT->value)
            ->where(Account::TABLE . '.' . Account::IS_INVOICING, true)
            ->orderBy(Account::TABLE . '.' . Account::SORT_VALUE)
            ->pluck(Account::TABLE . '.' . Account::ID)
            ->map(static fn($id) => (int) $id)
            ->all();
    }
}
