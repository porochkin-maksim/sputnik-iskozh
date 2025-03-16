<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Repositories;

use App\Models\Billing\Invoice;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Illuminate\Support\Facades\DB;

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

    public function getSummaryFor(?int $type, ?int $periodId, ?int $accountId): array
    {
        $outcome = InvoiceTypeEnum::OUTCOME->value;

        $result = Invoice::query()->select(
            DB::raw("
                SUM(CASE WHEN type = {$outcome} THEN 0 ELSE cost END)  AS income_cost,
                SUM(CASE WHEN type = {$outcome} THEN 0 ELSE payed END) AS income_payed,
                SUM(CASE WHEN type = {$outcome} THEN cost ELSE 0 END)  AS outcome_cost,
                SUM(CASE WHEN type = {$outcome} THEN payed ELSE 0 END) AS outcome_payed
            "),
        )->when($periodId, function ($query) use ($periodId) {
            $query->where(Invoice::PERIOD_ID, SearcherInterface::EQUALS, $periodId);
        })->when($accountId, function ($query) use ($accountId) {
            $query->where(Invoice::ACCOUNT_ID, SearcherInterface::EQUALS, $accountId);
        })->when($type, function ($query) use ($type) {
            $query->where(Invoice::TYPE, SearcherInterface::EQUALS, $type);
        })->first()?->toArray();

        return $result;
    }
}
