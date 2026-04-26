<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Claim;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class ClaimEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private ServiceEloquentMapper $serviceEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : Claim::make();

        return $result->fill([
            Claim::INVOICE_ID => $entity->getInvoiceId(),
            Claim::SERVICE_ID => $entity->getServiceId(),
            Claim::NAME       => $entity->getName(),
            Claim::TARIFF     => $entity->getTariff(),
            Claim::COST       => $entity->getCost(),
            Claim::PAID       => $entity->getPaid(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = new ClaimEntity();
        $result
            ->setId($data->id)
            ->setInvoiceId($data->invoice_id)
            ->setServiceId($data->service_id)
            ->setName($data->name)
            ->setTariff($data->tariff)
            ->setPaid($data->paid)
            ->setCost($data->cost)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;

        if (isset($data->getRelations()[Claim::RELATION_SERVICE])) {
            $result->setService($this->serviceEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Claim::RELATION_SERVICE)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new ClaimCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
