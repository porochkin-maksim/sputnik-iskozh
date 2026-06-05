<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Acquiring;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Acquiring\AcquiringCollection;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class AcquiringEloquentMapper implements RepositoryDataMapperInterface
{
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Acquiring::make();

        return $result->fill([
            Acquiring::INVOICE_ID => $entity->getInvoiceId(),
            Acquiring::USER_ID => $entity->getUserId(),
            Acquiring::PAYMENT_ID => $entity->getPaymentId(),
            Acquiring::PROVIDER => $entity->getProvider()?->value,
            Acquiring::STATUS => $entity->getStatus()?->value,
            Acquiring::AMOUNT => $entity->getAmount(),
            Acquiring::DATA => $entity->getData(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        return (new AcquiringEntity())
            ->setId($data->{Acquiring::ID})
            ->setInvoiceId($data->{Acquiring::INVOICE_ID})
            ->setUserId($data->{Acquiring::USER_ID})
            ->setPaymentId($data->{Acquiring::PAYMENT_ID})
            ->setProvider(ProviderEnum::tryFrom((int) $data->{Acquiring::PROVIDER}))
            ->setStatus(StatusEnum::tryFrom((int) $data->{Acquiring::STATUS}))
            ->setAmount($data->{Acquiring::AMOUNT})
            ->setData($data->{Acquiring::DATA} ?: [])
            ->setCreatedAt($data->{Acquiring::CREATED_AT})
            ->setUpdatedAt($data->{Acquiring::UPDATED_AT});
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new AcquiringCollection();

        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
