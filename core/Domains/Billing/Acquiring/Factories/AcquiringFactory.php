<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Factories;

use App\Models\Billing\Acquiring;
use Core\Domains\Billing\Acquiring\Collections\AcquiringCollection;
use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;
use Core\Domains\Billing\Acquiring\Services\ProviderSelector;
use Illuminate\Database\Eloquent\Collection;

class AcquiringFactory
{
    public function __construct() { }

    public function makeDefault(): AcquiringDTO
    {
        return new AcquiringDTO()
            ->setStatus(StatusEnum::NEW)
            ->setProvider(ProviderSelector::random())
        ;
    }

    public function makeModelFromDto(AcquiringDTO $dto, ?Acquiring $model = null): Acquiring
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Acquiring::make();
        }

        return $result->fill([
            Acquiring::INVOICE_ID => $dto->getInvoiceId(),
            Acquiring::USER_ID    => $dto->getUserId(),
            Acquiring::PAYMENT_ID => $dto->getPaymentId(),
            Acquiring::PROVIDER   => $dto->getProvider()->value,
            Acquiring::STATUS     => $dto->getStatus()->value,
            Acquiring::AMOUNT     => $dto->getAmount(),
            Acquiring::DATA       => $dto->getData(),
        ]);
    }

    public function makeDtoFromObject(Acquiring $model): AcquiringDTO
    {
        $result = new AcquiringDTO();

        $result
            ->setId($model->id)
            ->setInvoiceId($model->invoice_id)
            ->setUserId($model->user_id)
            ->setPaymentId($model->payment_id)
            ->setProvider(ProviderEnum::tryFrom($model->provider))
            ->setStatus(StatusEnum::tryFrom($model->status))
            ->setAmount($model->amount)
            ->setData($model->data)
        ;

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): AcquiringCollection
    {
        $result = new AcquiringCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}
