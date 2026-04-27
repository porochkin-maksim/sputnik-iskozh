<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Repositories;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\Factories\InvoiceFactory;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Responses\InvoiceSearchResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    use RepositoryTrait {
        adaptFieldName as traitAdaptFieldName;
    }

    private const string TABLE = Invoice::TABLE;

    public function __construct(
        private readonly InvoiceFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Invoice::class;
    }

    private function adaptFieldName(string $field): string
    {
        if ($field === 'account_sort') {
            return $field;
        }

        return $this->traitAdaptFieldName($field);
    }

    public function search(SearcherInterface $searcher): InvoiceSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new InvoiceCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new InvoiceSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?InvoiceDTO
    {
        /** @var null|Invoice $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(InvoiceDTO $dto): InvoiceDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }

    public function getAccountsWithoutRegularInvoice(int $periodId): Collection
    {
        return Account::select(['accounts.id'])
            ->whereDoesntHave('invoices', function ($query) use ($periodId) {
                // Условия для фильтров по счетам-фактурам (если нужны)
                $query->where('period_id', $periodId)
                    ->where('type', InvoiceTypeEnum::REGULAR->value)
                ;
            })
            ->where('accounts.id', '!=', AccountIdEnum::SNT->value)
            ->where('accounts.is_invoicing', true)
            ->orderBy('accounts.sort_value')
            ->get()
        ;
    }

    private function getQuery(Builder $query): Builder
    {
        $query->select(self::TABLE. '.*')
            ->join(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, Invoice::TABLE . '.' . Invoice::ACCOUNT_ID)
            ->selectSub(Account::TABLE . '.' . Account::SORT_VALUE, 'account_sort')
        ;

        return $query;
    }
}
