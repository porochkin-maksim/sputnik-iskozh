<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private InvoiceService $invoiceService,
        private AccountService $accountService,
    )
    {
    }

    public function execute(
        ?int            $limit,
        ?int            $offset,
        ?string         $sortField = null,
        ?string         $sortOrder = null,
        ?string         $paidStatus = null,
        ?int            $periodId = null,
        ?string         $account = null,
        null|int|string $type = null,
        bool            $withPayments = false,
    ): InvoiceSearchResponse
    {
        $searcher = new InvoiceSearcher();
        $searcher
            ->setLimit($limit)
            ->setOffset($offset)
            ->setWithPeriod()
            ->setWithAccount()
            ->setWithClaims()
        ;

        if ($withPayments) {
            $searcher->setWithPayments();
        }

        if ($sortField && $sortOrder) {
            $searcher->setSortOrderProperty(
                $sortField,
                $sortOrder === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(Invoice::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        if ($paidStatus === 'unpaid') {
            $searcher->addWhere(Invoice::PAID, SearcherInterface::EQUALS, 0);
        }
        elseif ($paidStatus === 'paid') {
            $searcher->addWhereColumn(Invoice::PAID, SearcherInterface::GTE, Invoice::COST);
        }
        elseif ($paidStatus === 'partial') {
            $searcher->addWhereColumn(Invoice::PAID, SearcherInterface::LT, Invoice::COST)
                ->addWhere(Invoice::PAID, SearcherInterface::GT, 0)
            ;
        }

        if ($periodId) {
            $searcher->setPeriodId($periodId);
        }

        if ($account) {
            $accountIds = $this->accountService->search(
                AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$account}%"),
            )->getItems()->getIds();
            $searcher->setAccountIds($accountIds);
        }

        if ($type) {
            $searcher->setType(is_numeric((string) $type) ? (int) $type : $type);
        }

        return $this->invoiceService->search($searcher);
    }
}
