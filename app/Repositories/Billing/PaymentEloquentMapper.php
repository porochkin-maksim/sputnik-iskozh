<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Payment;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\Files\FileEloquentMapper;
use App\Repositories\Shared\Relations\InvoicePaymentRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class PaymentEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private AccountEloquentMapper           $accountEloquentMapper,
        private FileEloquentMapper              $fileEloquentMapper,
        private InvoicePaymentRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : Payment::make();

        return $result->fill([
            Payment::INVOICE_ID => $entity->getInvoiceId(),
            Payment::ACCOUNT_ID => $entity->getAccountId(),
            Payment::COST       => $entity->getCost(),
            Payment::MODERATED  => $entity->isModerated(),
            Payment::VERIFIED   => $entity->isVerified(),
            Payment::COMMENT    => $entity->getComment(),
            Payment::NAME       => $entity->getName(),
            Payment::DATA       => $entity->getData(),
            Payment::PAID_AT    => $entity->getPaidAt() ? : $entity->getCreatedAt(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = $this->relationAssembler->makePaymentEntity($data);

        if (isset($data->getRelations()[Payment::INVOICE])) {
            $result->setInvoice($this->relationAssembler->makeInvoiceEntity($data->getRelation(Payment::INVOICE)));
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
