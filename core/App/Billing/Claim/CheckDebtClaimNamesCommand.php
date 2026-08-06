<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use App\Models\Billing\Claim;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceTypeEnum;

readonly class CheckDebtClaimNamesCommand
{
    public function __construct(
        private ClaimService          $claimService,
        private ServiceCatalogService $serviceService,
        private DebtMigrationService  $debtMigrationService,
    )
    {
    }

    /**
     * Проверяет и при необходимости исправляет названия долговых услуг (claims типа Долг).
     *
     * @return array{checked: int, broken: int, fixed: int, items: array<int, array{id: int, old: ?string, new: string}>}
     */
    public function execute(bool $fix = false): array
    {
        $debtServiceIds = $this->serviceService->search(
            (new ServiceSearcher)->setType(ServiceTypeEnum::DEBT),
        )->getItems()
            ->map(static fn(ServiceEntity $service) => $service->getId())
            ->toArray()
        ;

        if (empty($debtServiceIds)) {
            return ['checked' => 0, 'broken' => 0, 'fixed' => 0, 'items' => []];
        }

        $claims = $this->claimService->search(
            (new ClaimSearcher)
                ->setWithService()
                ->setWithOriginalService()
                ->addWhereIn(Claim::SERVICE_ID, $debtServiceIds),
        )->getItems();

        $checked = 0;
        $broken  = [];
        $fixed   = [];

        foreach ($claims as $claim) {
            $checked++;

            $currentName = $claim->getName();
            $correctName = $this->debtMigrationService->resolveDebtClaimName($claim);

            if ($currentName === $correctName) {
                continue;
            }

            $broken[] = ['id' => $claim->getId(), 'old' => $currentName, 'new' => $correctName];

            if ($fix) {
                $this->claimService->save($claim->setName($correctName));
                $fixed[] = $claim->getId();
            }
        }

        return [
            'checked' => $checked,
            'broken'  => count($broken),
            'fixed'   => count($fixed),
            'items'   => $broken,
        ];
    }
}
