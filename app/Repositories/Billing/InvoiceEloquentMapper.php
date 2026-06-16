<?php declare(strict_types=1);

namespace App\Repositories\Billing;

use App\Models\Billing\Invoice;
use App\Repositories\Account\AccountEloquentMapper;
use App\Repositories\Shared\Relations\InvoicePaymentRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

readonly class InvoiceEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private AccountEloquentMapper           $accountEloquentMapper,
        private PeriodEloquentMapper            $periodEloquentMapper,
        private ClaimEloquentMapper             $claimEloquentMapper,
        private InvoicePaymentRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ? : Invoice::make();

        return $result->fill([
            Invoice::PERIOD_ID  => $entity->getPeriodId(),
            Invoice::ACCOUNT_ID => $entity->getAccountId(),
            Invoice::SERVICE_ID => $entity->getServiceId(),
            Invoice::TYPE       => $entity->getType()?->value,
            Invoice::PAID       => (float) $entity->getPaid(),
            Invoice::COST       => (float) $entity->getCost(),
            Invoice::ADVANCE    => (float) $entity->getAdvance(),
            Invoice::DEBT       => (float) $entity->getDebt(),
            Invoice::ROUNDING   => (float) $entity->getRounding(),
            Invoice::NAME       => $entity->getName(),
            Invoice::COMMENT    => $entity->getComment(),
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = $this->relationAssembler->makeInvoiceEntity($data);

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
                $payments->add($this->relationAssembler->makePaymentEntity($paymentModel));
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
