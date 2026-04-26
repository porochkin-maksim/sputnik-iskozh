<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Acquiring;
use Core\Domains\Billing\Acquiring\AcquiringCollection;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
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
        return (new AcquiringEntity())
            ->setId($data->id)
            ->setInvoiceId($data->invoice_id)
            ->setUserId($data->user_id)
            ->setPaymentId($data->payment_id)
            ->setProvider(ProviderEnum::tryFrom((int) $data->provider))
            ->setStatus(StatusEnum::tryFrom((int) $data->status))
            ->setAmount($data->amount)
            ->setData($data->data ?: [])
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at);
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
