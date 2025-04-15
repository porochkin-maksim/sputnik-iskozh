<?php declare(strict_types=1);

namespace Core\Domains\Billing\Summary\Repositories;

use App\Models\Billing\Invoice;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Illuminate\Support\Facades\DB;

class SummaryRepository
{
    public function getSummaryFor(?int $type, ?int $periodId, ?array $accountIds): array
    {
        $regular = InvoiceTypeEnum::REGULAR->value;
        $income  = InvoiceTypeEnum::INCOME->value;
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
        })->when($accountIds, function ($query) use ($accountIds) {
            $query->whereIn(Invoice::ACCOUNT_ID, $accountIds);
        })->when($type, function ($query) use ($type) {
            $query->where(Invoice::TYPE, SearcherInterface::EQUALS, $type);
        })->first()?->toArray();

        return $result;
    }

    public function getClaimsFor(array $invoiceIds): array
    {
        $invoiceIdsString = implode(',', $invoiceIds);
        $otherType        = ServiceTypeEnum::OTHER->value;

        $sql = <<<SQL
            SELECT 
                IF(services.type = {$otherType} AND claims.name IS NOT NULL, claims.name, services.name) AS service,
                SUM(claims.cost) AS cost,
                SUM(claims.payed) AS payed,
                SUM(claims.cost) - SUM(claims.payed) AS delta
            FROM claims
            INNER JOIN services ON claims.service_id = services.id
            WHERE invoice_id IN ({$invoiceIdsString})
            GROUP BY service, services.type
            ORDER BY services.type;
            SQL;

        return DB::select($sql);
    }
}
