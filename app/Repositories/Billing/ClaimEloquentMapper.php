<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Claim;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
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
            Claim::INVOICE_ID          => $entity->getInvoiceId(),
            Claim::SERVICE_ID          => $entity->getServiceId(),
            Claim::ORIGINAL_SERVICE_ID => $entity->getOriginalServiceId(),
            Claim::ORIGINAL_CLAIM_ID   => $entity->getOriginalClaimId(),
            Claim::NAME                => $entity->getName(),
            Claim::TARIFF              => $entity->getTariff(),
            Claim::COST                => $entity->getCost(),
            Claim::PAID                => $entity->getPaid() ?? 0,
            Claim::QUANTITY            => $entity->getQuantity(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = new ClaimEntity();
        $result
            ->setId($data->{Claim::ID})
            ->setInvoiceId($data->{Claim::INVOICE_ID})
            ->setServiceId($data->{Claim::SERVICE_ID})
            ->setOriginalServiceId($data->{Claim::ORIGINAL_SERVICE_ID})
            ->setOriginalClaimId($data->{Claim::ORIGINAL_CLAIM_ID})
            ->setName($data->{Claim::NAME})
            ->setTariff($data->{Claim::TARIFF})
            ->setPaid((float) ($data->{Claim::PAID} ?? 0))
            ->setCost($data->{Claim::COST})
            ->setQuantity($data->{Claim::QUANTITY})
            ->setCreatedAt($data->{Claim::CREATED_AT})
            ->setUpdatedAt($data->{Claim::UPDATED_AT})
        ;

        if (isset($data->getRelations()[Claim::RELATION_SERVICE])) {
            $result->setService($this->serviceEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Claim::RELATION_SERVICE)));
        }

        if (isset($data->getRelations()[Claim::RELATION_ORIGINAL_SERVICE])) {
            $result->setOriginalService($this->serviceEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Claim::RELATION_ORIGINAL_SERVICE)));
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
