<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Factories;

use App\Models\Billing\Transaction;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Illuminate\Database\Eloquent\Collection;

readonly class TransactionFactory
{
    public function makeDefault(): TransactionDTO
    {
        return (new TransactionDTO())
            ->setTariff(0.00)
            ->setCost(0.00)
            ->setPayed(0.00);
    }

    public function makeModelFromDto(TransactionDTO $dto, ?Transaction $model = null): Transaction
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Transaction::make();
        }

        return $result->fill([
            Transaction::INVOICE_ID => $dto->getInvoiceId(),
            Transaction::SERVICE_ID => $dto->getServiceId(),
            Transaction::NAME       => $dto->getName(),
            Transaction::TARIFF     => $dto->getTariff(),
            Transaction::COST       => $dto->getCost(),
            Transaction::PAYED      => $dto->getPayed(),
        ]);
    }

    public function makeDtoFromObject(Transaction $model): TransactionDTO
    {
        $result = new TransactionDTO();

        $result
            ->setId($model->id)
            ->setInvoiceId($model->invoice_id)
            ->setServiceId($model->service_id)
            ->setName($model->name)
            ->setTariff($model->tariff)
            ->setPayed($model->payed)
            ->setCost($model->cost)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);


        if (isset($model->getRelations()[Transaction::SERVICE])) {
            $result->setService(ServiceLocator::ServiceFactory()->makeDtoFromObject($model->getRelation(Transaction::SERVICE)));
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): TransactionCollection
    {
        $result = new TransactionCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}