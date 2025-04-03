<?php declare(strict_types=1);

namespace Core\Domains\Counter\Factories;

use App\Models\Billing\TransactionToObject;
use App\Models\Counter\CounterHistory;
use Carbon\Carbon;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\File\FileLocator;
use Illuminate\Database\Eloquent\Collection;

readonly class CounterHistoryFactory
{
    public function makeDefault(): CounterHistoryDTO
    {
        return (new CounterHistoryDTO())
            ->setIsVerified(false)
            ->setDate(Carbon::now())
        ;
    }

    public function makeModelFromDto(CounterHistoryDTO $dto, ?CounterHistory $model = null): CounterHistory
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = CounterHistory::make();
        }

        $result->forceFill([
            'id' => $dto->getId(),
        ]);

        return $result->fill([
            CounterHistory::COUNTER_ID  => $dto->getCounterId(),
            CounterHistory::PREVIOUS_ID => $dto->getPreviousId(),
            CounterHistory::VALUE       => $dto->getValue(),
            CounterHistory::DATE        => $dto->getDate(),
            CounterHistory::IS_VERIFIED => $dto->isVerified(),
        ]);
    }

    public function makeDtoFromObject(CounterHistory $model): CounterHistoryDTO
    {
        $result = new CounterHistoryDTO();

        $result
            ->setId($model->id)
            ->setCounterId($model->counter_id)
            ->setPreviousId($model->previous_id)
            ->setValue($model->value)
            ->setDate($model->date)
            ->setIsVerified($model->is_verified)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        if (isset($model->getRelations()[CounterHistory::PREVIOUS])) {
            $result->setPreviousValue($model->getRelation(CounterHistory::PREVIOUS)->value);
        }

        if (isset($model->getRelations()[CounterHistory::FILE])) {
            $result->setFile(FileLocator::FileFactory()->makeDtoFromObject($model->getRelation(CounterHistory::FILE)));
        }

        if (isset($model->getRelations()[CounterHistory::COUNTER])) {
            $result->setCounter(CounterLocator::CounterFactory()->makeDtoFromObject($model->getRelation(CounterHistory::COUNTER)));
        }

        if (isset($model->getRelations()[CounterHistory::TRANSACTON])) {
            $transaction = $model->getRelation(CounterHistory::TRANSACTON);
            if (isset($transaction->getRelations()[TransactionToObject::TRANSACTION])) {
                $result->setTransaction(TransactionLocator::TransactionFactory()->makeDtoFromObject($transaction->getRelation(TransactionToObject::TRANSACTION)));
            }
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): CounterHistoryCollection
    {
        $result = new CounterHistoryCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}