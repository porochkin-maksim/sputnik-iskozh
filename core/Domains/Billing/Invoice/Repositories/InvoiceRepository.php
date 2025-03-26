<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Repositories;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use Core\Db\RepositoryTrait;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    private const TABLE = Invoice::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
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
                    ->where('type', InvoiceTypeEnum::REGULAR->value);
            })
            ->where('accounts.id', '!=', AccountIdEnum::SNT->value)
            ->get()
        ;
    }
}
