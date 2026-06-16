<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Transaction;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Transaction\TransactionCollection;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class TransactionEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private TransactionFactory    $transactionFactory,
        private PaymentEloquentMapper $paymentEloquentMapper,
        private ClaimEloquentMapper   $claimEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : Transaction::make())->fill([
            Transaction::PAYMENT_ID => $entity->getPaymentId(),
            Transaction::CLAIM_ID   => $entity->getClaimId(),
            Transaction::COST       => $entity->getCost(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var Transaction $data */
        $result = $this->transactionFactory->makeDefault()
            ->setId($data->{Transaction::ID})
            ->setPaymentId($data->{Transaction::PAYMENT_ID})
            ->setClaimId($data->{Transaction::CLAIM_ID})
            ->setCost($data->{Transaction::COST})
            ->setCreatedAt($data->{Transaction::CREATED_AT})
        ;

        if (isset($data->getRelations()[Transaction::PAYMENT])) {
            $result->setPayment(
                $this->paymentEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Transaction::PAYMENT)),
            );
        }

        if (isset($data->getRelations()[Transaction::CLAIM])) {
            $result->setClaim(
                $this->claimEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Transaction::CLAIM)),
            );
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new TransactionCollection();

        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
