<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Factories;

use App\Models\Billing\Service;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;

readonly class ServiceFactory
{
    public function makeDefault(): ServiceDTO
    {
        return (new ServiceDTO())
            ->setId(null)
            ->setPeriodId(null)
            ->setType(ServiceTypeEnum::TARGET_FEE)
            ->setName('')
            ->setIsActive(true)
            ->setCost(0);
    }

    public function makeModelFromDto(ServiceDTO $dto, ?Service $model = null): Service
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Service::make();
        }

        return $result->fill([
            Service::TYPE      => $dto->getType()?->value,
            Service::PERIOD_ID => $dto->getPeriodId(),
            Service::NAME      => $dto->getName(),
            Service::COST      => $dto->getCost(),
            Service::ACTIVE    => $dto->isActive(),
        ]);
    }

    public function makeDtoFromObject(Service $model): ServiceDTO
    {
        $result = new ServiceDTO();

        $result
            ->setId($model->id)
            ->setType(ServiceTypeEnum::tryFrom($model->type))
            ->setPeriodId($model->period_id)
            ->setName($model->name)
            ->setCost($model->cost)
            ->setIsActive($model->active)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[Service::PERIOD])) {
            $result->setPeriod(PeriodLocator::PeriodFactory()->makeDtoFromObject($model->getRelation(Service::PERIOD)));
        }

        return $result;
    }
}