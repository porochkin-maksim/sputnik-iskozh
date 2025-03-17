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
        $regular = InvoiceTypeEnum::REGULAR->value;
        $income = InvoiceTypeEnum::INCOME->value;
        $outcome = InvoiceTypeEnum::OUTCOME->value;

        $result = Invoice::query()->select(
            DB::raw("
                COUNT(CASE WHEN type = {$regular} THEN 1 ELSE NULL END) AS regularCount,
                COUNT(CASE WHEN type = {$income} THEN 1 ELSE NULL END)   AS incomeCount,
                COUNT(CASE WHEN type = {$outcome} THEN 1 ELSE NULL END) AS outcomeCount,
                
                SUM(CASE WHEN type = {$outcome} THEN 0 ELSE cost END)  AS incomeCost,
                SUM(CASE WHEN type = {$outcome} THEN 0 ELSE payed END) AS incomePayed,
                SUM(CASE WHEN type = {$outcome} THEN cost ELSE 0 END)  AS outcomeCost,
                SUM(CASE WHEN type = {$outcome} THEN payed ELSE 0 END) AS outcomePayed
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
