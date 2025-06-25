<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Repositories;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    private const string TABLE = Invoice::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
        adaptFieldName as traitAdaptFieldName;
    }

    private function adaptFieldName(string $field): string
    {
        if ($field === 'account_sort') {
            return $field;
        }

        return $this->traitAdaptFieldName($field);
    }

    protected function modelClass(): string
    {
        return Invoice::class;
    }

    public function getById(?int $id): ?Invoice
    {
        /** @var ?Invoice $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Invoice $invoice): Invoice
    {
        $invoice->save();

        return $invoice;
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
