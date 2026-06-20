<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Contracts\DbServiceInterface;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionService;
use Throwable;

readonly class ResetPeriodPaymentsCommand
{
    public function __construct(
        private InvoiceService     $invoiceService,
        private ClaimService       $claimService,
        private TransactionService $transactionService,
        private TransactionFactory $transactionFactory,
        private DbServiceInterface $dbService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(int $periodId): array
    {
        return $this->dbService->transaction(function () use ($periodId) {
            $invoices = $this->invoiceService->getByPeriodId($periodId);

            $claimsReset           = 0;
            $transactionsCollapsed = 0;
            $invoicesUpdated       = 0;

            foreach ($invoices as $invoice) {
                $claims = $this->claimService->getByInvoiceId($invoice->getId());

                if ($claims->isEmpty()) {
                    $invoice->setPaid(0);
                    $this->invoiceService->save($invoice);
                    $invoicesUpdated++;
                    continue;
                }

                foreach ($claims as $claim) {
                    $claim->setPaid(0);
                }
                $this->claimService->saveCollection($claims);
                $claimsReset++;

                $claimIds = $claims->getIds();

                $txs = $this->transactionService->getByClaimsIds($claimIds);

                if ($txs->isNotEmpty()) {
                    $grouped = [];
                    foreach ($txs as $tx) {
                        $pid = $tx->getPaymentId();
                        if ($pid !== null) {
                            $grouped[$pid] = ($grouped[$pid] ?? 0.0) + (float) $tx->getCost();
                        }
                    }

                    $transactionsCollapsed += count($txs);

                    foreach ($txs as $tx) {
                        $this->transactionService->deleteById($tx->getId());
                    }

                    foreach ($grouped as $paymentId => $totalCost) {
                        $existing = $this->transactionService->getUnallocatedBypaymentId($paymentId)->first();

                        if ($existing) {
                            $existing->setCost((float) $existing->getCost() + $totalCost);
                            $this->transactionService->save($existing);
                        }
                        else {
                            $tx = $this->transactionFactory->makeDefault()
                                ->setPaymentId($paymentId)
                                ->setClaimId(null)
                                ->setCost($totalCost)
                            ;
                            $this->transactionService->save($tx);
                        }
                    }
                }

                $invoice->setPaid(0);
                $this->invoiceService->save($invoice);
                $invoicesUpdated++;
            }

            return [
                'claims_reset'           => $claimsReset > 0 || $invoicesUpdated > 0,
                'transactions_collapsed' => $transactionsCollapsed,
                'invoices_updated'       => $invoicesUpdated,
            ];
        });
    }
}
