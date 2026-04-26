<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;

readonly class SaveCommand
{
    public function __construct(
        private ServiceFactory        $serviceFactory,
        private ServiceCatalogService $serviceService,
        private SaveValidator         $validator,
    )
    {
    }

    public function execute(
        ?int             $id,
        ?int             $periodId,
        ?ServiceTypeEnum $type,
        ?string          $name,
        ?float           $cost,
        bool             $isActive,
    ): ?ServiceEntity
    {
        $this->validator->validate($periodId, $type, $name, $cost);

        $service = $id
            ? $this->serviceService->getById($id)
            : $this->serviceFactory->makeDefault()
                ->setPeriodId($periodId)
                ->setType($type)
        ;

        if ($service === null) {
            return null;
        }

        $service
            ->setName($name)
            ->setIsActive($isActive)
            ->setCost($cost)
        ;

        return $this->serviceService->save($service);
    }
}
