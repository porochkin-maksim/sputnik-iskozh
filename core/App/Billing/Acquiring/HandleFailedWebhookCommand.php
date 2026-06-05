<?php declare(strict_types=1);

namespace Core\App\Billing\Acquiring;

use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;

readonly class HandleFailedWebhookCommand
{
    public function __construct(
        private AcquiringService $acquiringService,
    )
    {
    }

    public function execute(int $acquiringId, string $hash): bool
    {
        $acquiring = $this->acquiringService->getById($acquiringId);

        if ($acquiring === null || $acquiring->makeHash() !== $hash || ! $acquiring->getStatus()?->isProcess()) {
            return false;
        }

        $acquiring->setStatus(StatusEnum::CANCELED);
        $this->acquiringService->save($acquiring);

        return true;
    }
}
