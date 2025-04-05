<?php declare(strict_types=1);

namespace Core\Domains\Billing\Summary\Services;

use Core\Domains\Billing\Summary\Models\Summary;
use Core\Domains\Billing\Summary\Repositories\SummaryRepository;

readonly class SummaryService
{
    public function __construct(
        private SummaryRepository $summaryRepository,
    )
    {
    }

    public function getSummaryFor(?int $type, ?int $periodId, ?int $accountId): Summary
    {
        return new Summary($this->summaryRepository->getSummaryFor($type, $periodId, $accountId));
    }

    public function getClaimsFor(array $invoiceIds): array
    {
        return $this->summaryRepository->getClaimsFor($invoiceIds);
    }
}
