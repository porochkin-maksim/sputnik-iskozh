<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Access\PermissionEnum;
use lc;

readonly class PaymentGate
{
    public function __construct(
        private PaymentTransactionService $paymentTransactionService,
    )
    {
    }

    public function assertCanEdit(?int $paymentId): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_EDIT)) {
            abort(403);
        }

        if ($paymentId && $this->paymentTransactionService->hasAllocatedTransactions($paymentId)) {
            abort(403, 'Нельзя редактировать платёж с распределёнными транзакциями');
        }
    }
}
