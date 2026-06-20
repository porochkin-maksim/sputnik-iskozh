<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Core\Domains\Access\PermissionEnum;
use lc;

readonly class PeriodGate
{
    public function __construct(
        private PeriodService $periodService,
    )
    {
    }

    public function assertCanEditInvoices(int $periodId): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::INVOICES_EDIT)) {
            abort(403);
        }

        $this->assertNotClosed($periodId);
    }

    public function assertCanEditServices(int $periodId): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::SERVICES_EDIT)) {
            abort(403);
        }

        $this->assertNotClosed($periodId);
    }

    public function assertNotClosed(int $periodId): void
    {
        $period = $this->periodService->getById($periodId);

        if ( ! $period) {
            abort(404, 'Период не найден');
        }

        if ($period->isClosed()) {
            abort(403, 'Период закрыт');
        }
    }
}
