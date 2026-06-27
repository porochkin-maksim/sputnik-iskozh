<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use Carbon\Carbon;
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
        ?Carbon          $periodFrom = null,
        ?Carbon          $periodTo = null,
    ): ?ServiceEntity
    {
        $this->validator->validate($periodId, $type, $name, $cost, $periodFrom, $periodTo);

        $service = $id
            ? $this->serviceService->getById($id)
            : $this->serviceFactory->makeDefault()
                ->setPeriodId($periodId)
        ;

        if ($service === null) {
            return null;
        }

        $service
            ->setName($name)
            ->setType($type)
            ->setCost($cost)
            ->setPeriodFrom($periodFrom)
            ->setPeriodTo($periodTo)
        ;

        return $this->serviceService->save($service);
    }
}
