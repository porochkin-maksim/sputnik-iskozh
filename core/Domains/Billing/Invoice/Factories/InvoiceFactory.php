<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Factories;

use App\Models\Billing\Invoice;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Claim\ClaimLocator;

readonly class InvoiceFactory
{
    public function makeDefault(): InvoiceDTO
    {
        return new InvoiceDTO()
            ->setCost(0.00)
            ->setPayed(0.00);
    }

    public function makeModelFromDto(InvoiceDTO $dto, ?Invoice $model = null): Invoice
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Invoice::make();
        }

        return $result->fill([
            Invoice::PERIOD_ID  => $dto->getPeriodId(),
            Invoice::ACCOUNT_ID => $dto->getAccountId(),
            Invoice::TYPE       => $dto->getType()->value,
            Invoice::PAYED      => $dto->getPayed(),
            Invoice::COST       => $dto->getCost(),
            Invoice::NAME       => $dto->getName(),
            Invoice::COMMENT    => $dto->getComment(),
        ]);
    }

    public function makeDtoFromObject(Invoice $model): InvoiceDTO
    {
        $result = new InvoiceDTO();

        $result
            ->setId($model->id)
            ->setPeriodId($model->period_id)
            ->setAccountId($model->account_id)
            ->setPayed($model->payed)
            ->setCost($model->cost)
            ->setType(InvoiceTypeEnum::tryFrom($model->type))
            ->setName($model->name)
            ->setComment($model->comment)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[Invoice::CLAIMS])) {
            $result->setClaims(ClaimLocator::ClaimFactory()->makeDtoFromObjects($model->getRelation(Invoice::CLAIMS)));
        }

        if (isset($model->getRelations()[Invoice::PAYMENTS])) {
            $result->setPayments(PaymentLocator::PaymentFactory()->makeDtoFromObjects($model->getRelation(Invoice::PAYMENTS)));
        }

        if (isset($model->getRelations()[Invoice::ACCOUNT])) {
            $result->setAccount(AccountLocator::AccountFactory()->makeDtoFromObject($model->getRelation(Invoice::ACCOUNT)));
        }

        if (isset($model->getRelations()[Invoice::PERIOD])) {
            $result->setPeriod(PeriodLocator::PeriodFactory()->makeDtoFromObject($model->getRelation(Invoice::PERIOD)));
        }

        return $result;
    }
}
