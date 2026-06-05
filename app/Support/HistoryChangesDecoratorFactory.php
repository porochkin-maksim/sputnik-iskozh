<?php declare(strict_types=1);

namespace App\Support;

use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Counter\CounterService;
use Core\Domains\HistoryChanges\HistoryChangesDecorator;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesService;

readonly class HistoryChangesDecoratorFactory
{
    public function __construct(
        private CounterService $counterService,
        private PaymentService $paymentService,
        private ClaimService $claimService,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function make(HistoryChangesEntity $historyChanges): HistoryChangesDecorator
    {
        return new HistoryChangesDecorator(
            $historyChanges,
            $this->counterService,
            $this->paymentService,
            $this->claimService,
            $this->historyChangesService,
        );
    }
}
