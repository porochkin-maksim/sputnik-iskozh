<?php declare(strict_types=1);

namespace Core\Domains\Counter\Factories;

use App\Models\Billing\ClaimToObject;
use App\Models\Counter\CounterHistory;
use Carbon\Carbon;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Files\FileLocator;
use Illuminate\Database\Eloquent\Collection;

class CounterHistoryFactory
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
            CounterHistory::COUNTER_ID     => $dto->getCounterId(),
            CounterHistory::PREVIOUS_ID    => $dto->getPreviousId(),
            CounterHistory::PREVIOUS_VALUE => $dto->getPreviousValue(),
            CounterHistory::VALUE          => $dto->getValue(),
            CounterHistory::DATE           => $dto->getDate(),
            CounterHistory::IS_VERIFIED    => $dto->isVerified(),
        ]);
    }

    public function makeDtoFromObject(CounterHistory $model): CounterHistoryDTO
    {
        $result = new CounterHistoryDTO();

        $result
            ->setId($model->id)
            ->setCounterId($model->counter_id)
            ->setPreviousId($model->previous_id)
            ->setPreviousValue($model->previous_value)
            ->setValue($model->value)
            ->setDate($model->date)
            ->setIsVerified($model->is_verified)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        if (isset($model->getRelations()[CounterHistory::RELATION_PREVIOUS])) {
            $result->setPrevious($this->makeDtoFromObject($model->getRelation(CounterHistory::RELATION_PREVIOUS)));
        }

        if (isset($model->getRelations()[CounterHistory::RELATION_FILE])) {
            $result->setFile(FileLocator::FileFactory()->makeDtoFromObject($model->getRelation(CounterHistory::RELATION_FILE)));
        }

        if (isset($model->getRelations()[CounterHistory::RELATION_COUNTER])) {
            $result->setCounter(CounterLocator::CounterFactory()->makeDtoFromObject($model->getRelation(CounterHistory::RELATION_COUNTER)));
        }

        if (isset($model->getRelations()[CounterHistory::RELATION_CLAIM])) {
            $claim = $model->getRelation(CounterHistory::RELATION_CLAIM);
            if (isset($claim->getRelations()[ClaimToObject::RELATION_CLAIM])) {
                $result->setClaim(ClaimLocator::ClaimFactory()->makeDtoFromObject($claim->getRelation(ClaimToObject::RELATION_CLAIM)));
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