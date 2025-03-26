<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Factories;

use App\Models\Billing\Payment;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Domains\File\FileLocator;
use Illuminate\Database\Eloquent\Collection;

readonly class PaymentFactory
{
    public function makeDefault(): PaymentDTO
    {
        return (new PaymentDTO())
            ->setModerated(false)
            ->setVerified(false)
            ->setCost(0.00)
        ;
    }

    public function makeModelFromDto(PaymentDTO $dto, ?Payment $model = null): Payment
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Payment::make();
        }

        return $result->fill([
            Payment::INVOICE_ID => $dto->getInvoiceId(),
            Payment::ACCOUNT_ID => $dto->getAccountId(),
            Payment::COST       => $dto->getCost(),
            Payment::MODERATED  => $dto->isModerated(),
            Payment::VERIFIED   => $dto->isVerified(),
            Payment::COMMENT    => $dto->getComment(),
            Payment::NAME       => $dto->getName(),
            Payment::DATA       => $dto->getData(),
        ]);
    }

    public function makeDtoFromObject(Payment $model): PaymentDTO
    {
        $result = new PaymentDTO();

        $result
            ->setId($model->id)
            ->setInvoiceId($model->invoice_id)
            ->setAccountId($model->account_id)
            ->setCost($model->cost)
            ->setModerated($model->moderated)
            ->setVerified($model->verified)
            ->setComment($model->comment)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
            ->setName($model->name)
            ->setData($model->data)
        ;

        if (isset($model->getRelations()[Payment::INVOICE])) {
            $result->setInvoice(InvoiceLocator::InvoiceFactory()->makeDtoFromObject($model->getRelation(Payment::INVOICE)));
        }
        if (isset($model->getRelations()[Payment::ACCOUNT])) {
            $result->setAccount(AccountLocator::AccountFactory()->makeDtoFromObject($model->getRelation(Payment::ACCOUNT)));
        }
        if (isset($model->getRelations()[Payment::FILES])) {
            $result->setFiles(FileLocator::FileFactory()->makeDtoFromObjects($model->getRelation(Payment::FILES)));
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): PaymentCollection
    {
        $result = new PaymentCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}