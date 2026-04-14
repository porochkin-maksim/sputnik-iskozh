<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Factories;

use App\Models\Billing\Claim;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Service\ServiceLocator;
use Illuminate\Database\Eloquent\Collection;

readonly class ClaimFactory
{
    public function makeDefault(): ClaimDTO
    {
        return new ClaimDTO()
            ->setTariff(0.00)
            ->setCost(0.00)
            ->setPaid(0.00);
    }

    public function makeModelFromDto(ClaimDTO $dto, ?Claim $model = null): Claim
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Claim::make();
        }

        return $result->fill([
            Claim::INVOICE_ID => $dto->getInvoiceId(),
            Claim::SERVICE_ID => $dto->getServiceId(),
            Claim::NAME       => $dto->getName(),
            Claim::TARIFF     => $dto->getTariff(),
            Claim::COST       => $dto->getCost(),
            Claim::PAID       => $dto->getPaid(),
        ]);
    }

    public function makeDtoFromObject(Claim $model): ClaimDTO
    {
        $result = new ClaimDTO();

        $result
            ->setId($model->id)
            ->setInvoiceId($model->invoice_id)
            ->setServiceId($model->service_id)
            ->setName($model->name)
            ->setTariff($model->tariff)
            ->setPaid($model->paid)
            ->setCost($model->cost)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);


        if (isset($model->getRelations()[Claim::RELATION_SERVICE])) {
            $result->setService(ServiceLocator::ServiceFactory()->makeDtoFromObject($model->getRelation(Claim::RELATION_SERVICE)));
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): ClaimCollection
    {
        $result = new ClaimCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}