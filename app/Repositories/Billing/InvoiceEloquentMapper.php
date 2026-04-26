<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Invoice;
use App\Repositories\Account\AccountEloquentMapper;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class InvoiceEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private AccountEloquentMapper $accountEloquentMapper,
        private PeriodEloquentMapper  $periodEloquentMapper,
        private ClaimEloquentMapper   $claimEloquentMapper,
        private PaymentEloquentMapper $paymentEloquentMapper,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : Invoice::make();

        return $result->fill([
            Invoice::PERIOD_ID  => $entity->getPeriodId(),
            Invoice::ACCOUNT_ID => $entity->getAccountId(),
            Invoice::TYPE       => $entity->getType()?->value,
            Invoice::PAID       => (float) $entity->getPaid(),
            Invoice::COST       => (float) $entity->getCost(),
            Invoice::ADVANCE    => (float) $entity->getAdvance(),
            Invoice::DEBT       => (float) $entity->getDebt(),
            Invoice::NAME       => $entity->getName(),
            Invoice::COMMENT    => $entity->getComment(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = new InvoiceEntity();
        $result
            ->setId($data->{Invoice::ID})
            ->setPeriodId($data->{Invoice::PERIOD_ID})
            ->setAccountId($data->{Invoice::ACCOUNT_ID})
            ->setPaid($data->{Invoice::PAID})
            ->setCost($data->{Invoice::COST})
            ->setAdvance($data->{Invoice::ADVANCE})
            ->setDebt($data->{Invoice::DEBT})
            ->setType(InvoiceTypeEnum::tryFrom($data->{Invoice::TYPE}))
            ->setName($data->{Invoice::NAME})
            ->setComment($data->{Invoice::COMMENT})
            ->setCreatedAt($data->{Invoice::CREATED_AT})
            ->setUpdatedAt($data->{Invoice::UPDATED_AT})
        ;

        if (isset($data->getRelations()[Invoice::RELATION_CLAIMS])) {
            $claims = new ClaimCollection();
            foreach ($data->getRelation(Invoice::RELATION_CLAIMS) as $claimModel) {
                $claims->add($this->claimEloquentMapper->makeEntityFromRepositoryData($claimModel));
            }
            $result->setClaims($claims);
        }

        if (isset($data->getRelations()[Invoice::RELATION_PAYMENTS])) {
            $payments = new PaymentCollection();
            foreach ($data->getRelation(Invoice::RELATION_PAYMENTS) as $paymentModel) {
                $payments->add($this->paymentEloquentMapper->makeEntityFromRepositoryData($paymentModel));
            }
            $result->setPayments($payments);
        }

        if (isset($data->getRelations()[Invoice::RELATION_ACCOUNT])) {
            $result->setAccount($this->accountEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Invoice::RELATION_ACCOUNT)));
        }

        if (isset($data->getRelations()[Invoice::RELATION_PERIOD])) {
            $result->setPeriod($this->periodEloquentMapper->makeEntityFromRepositoryData($data->getRelation(Invoice::RELATION_PERIOD)));
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new InvoiceCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
