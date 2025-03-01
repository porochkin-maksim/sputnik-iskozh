<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Factories;

use App\Models\Billing\Period;
use Carbon\Carbon;
use Core\Domains\Billing\Period\Models\PeriodDTO;

readonly class PeriodFactory
{
    public function makeDefault(): PeriodDTO
    {
        $startAt = Carbon::now()->setMonth(6)->startOfMonth()->startOfDay();
        $endAt   = Carbon::now()->setMonth(6)->startOfMonth()->startOfDay()->addYear()->subSecond();
        $name    = $startAt->format('Y') . ' - ' . $endAt->format('Y');

        return (new PeriodDTO())
            ->setName($name)
            ->setStartAt($startAt)
            ->setEndAt($endAt);
    }

    public function makeModelFromDto(PeriodDTO $dto, ?Period $model = null): Period
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Period::make();
        }

        return $result->fill([
            Period::NAME     => $dto->getName(),
            Period::START_AT => $dto->getStartAt(),
            Period::END_AT   => $dto->getEndAt(),
        ]);
    }

    public function makeDtoFromObject(Period $model): PeriodDTO
    {
        $result = new PeriodDTO();

        $result
            ->setId($model->id)
            ->setName($model->name)
            ->setStartAt($model->start_at)
            ->setEndAt($model->end_at)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        return $result;
    }
}