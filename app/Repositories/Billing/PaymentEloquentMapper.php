<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Payment;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class PaymentEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly AccountEloquentMapper $accountEloquentMapper,
        private readonly FileEloquentMapper $fileEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: Payment::make();

        return $result->fill([
            Payment::INVOICE_ID => $entity->getInvoiceId(),
            Payment::ACCOUNT_ID => $entity->getAccountId(),
            Payment::COST => $entity->getCost(),
            Payment::MODERATED => $entity->isModerated(),
            Payment::VERIFIED => $entity->isVerified(),
            Payment::COMMENT => $entity->getComment(),
            Payment::NAME => $entity->getName(),
            Payment::DATA => $entity->getData(),
            Payment::PAID_AT => $entity->getPaidAt() ?: $entity->getCreatedAt(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = new PaymentEntity();
        $result
            ->setId($data->id)
            ->setInvoiceId($data->invoice_id)
            ->setAccountId($data->account_id)
            ->setCost($data->cost)
            ->setModerated($data->moderated)
            ->setVerified($data->verified)
            ->setComment($data->comment)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
            ->setName($data->name)
            ->setData($data->data)
            ->setPaidAt($data->paid_at)
            ->setAccountNumber($data->account_number);

        if (isset($data->getRelations()[Payment::INVOICE])) {
            $result->setInvoice(app(InvoiceEloquentMapper::class)->makeEntityFromRepositoryData($data->getRelation(Payment::INVOICE)));
        }

        if (isset($data->getRelations()[Payment::ACCOUNT])) {
            $result->setAccount($this->accountEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Payment::ACCOUNT)));
        }

        if (isset($data->getRelations()[Payment::FILES])) {
            $result->setFiles($this->fileEloquentMapper->makeEntityFromRepositoryDatas($data->getRelation(Payment::FILES)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new PaymentCollection();

        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
