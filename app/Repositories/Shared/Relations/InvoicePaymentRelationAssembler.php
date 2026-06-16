<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\Billing\Invoice as InvoiceModel;
use App\Models\Billing\Payment as PaymentModel;
use App\Repositories\Billing\ServiceEloquentMapper;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;

readonly class InvoicePaymentRelationAssembler
{
    public function __construct(
        private InvoiceFactory        $invoiceFactory,
        private PaymentFactory        $paymentFactory,
        private ServiceEloquentMapper $serviceEloquentMapper,
    )
    {
    }

    public function makeInvoiceEntity(InvoiceModel $data): InvoiceEntity
    {
        $result = $this->invoiceFactory->makeDefault()
            ->setId($data->{InvoiceModel::ID})
            ->setPeriodId($data->{InvoiceModel::PERIOD_ID})
            ->setAccountId($data->{InvoiceModel::ACCOUNT_ID})
            ->setServiceId($data->{InvoiceModel::SERVICE_ID})
            ->setPaid($data->{InvoiceModel::PAID})
            ->setCost($data->{InvoiceModel::COST})
            ->setAdvance($data->{InvoiceModel::ADVANCE})
            ->setDebt($data->{InvoiceModel::DEBT})
            ->setRounding($data->{InvoiceModel::ROUNDING})
            ->setType(InvoiceTypeEnum::tryFrom($data->{InvoiceModel::TYPE}))
            ->setName($data->{InvoiceModel::NAME})
            ->setComment($data->{InvoiceModel::COMMENT})
            ->setCreatedAt($data->{InvoiceModel::CREATED_AT})
            ->setUpdatedAt($data->{InvoiceModel::UPDATED_AT})
        ;

        if (isset($data->getRelations()[InvoiceModel::RELATION_SERVICE])) {
            $result->setService($this->serviceEloquentMapper->makeEntityFromRepositoryData($data->getRelation(InvoiceModel::RELATION_SERVICE)));
        }

        return $result;
    }

    public function makePaymentEntity(PaymentModel $data): PaymentEntity
    {
        return $this->paymentFactory->makeDefault()
            ->setId($data->{PaymentModel::ID})
            ->setInvoiceId($data->{PaymentModel::INVOICE_ID})
            ->setAccountId($data->{PaymentModel::ACCOUNT_ID})
            ->setCost($data->{PaymentModel::COST})
            ->setModerated($data->{PaymentModel::MODERATED})
            ->setVerified($data->{PaymentModel::VERIFIED})
            ->setComment($data->{PaymentModel::COMMENT})
            ->setCreatedAt($data->{PaymentModel::CREATED_AT})
            ->setUpdatedAt($data->{PaymentModel::UPDATED_AT})
            ->setName($data->{PaymentModel::NAME})
            ->setData($data->{PaymentModel::DATA})
            ->setPaidAt($data->{PaymentModel::PAID_AT})
            ->setAccountNumber($data->account_number)
        ;
    }
}
