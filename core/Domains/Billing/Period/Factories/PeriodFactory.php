<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Factories;

use App\Models\Billing\Period;
use Carbon\Carbon;
use Core\Domains\Billing\Period\Models\PeriodDTO;

readonly class PeriodFactory
{
    public function makeDefault(): PeriodDTO
    {
        $year    = Carbon::now()->year;
        $startAt = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $endAt   = Carbon::createFromDate($year, 12, 31)->endOfDay();

        return (new PeriodDTO())
            ->setName((string) $year)
            ->setStartAt($startAt)
            ->setEndAt($endAt)
            ->setIsClosed(false)
        ;
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
            Period::NAME      => $dto->getName(),
            Period::IS_CLOSED => $dto->isClosed(),
            Period::START_AT  => $dto->getStartAt(),
            Period::END_AT    => $dto->getEndAt(),
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
            ->setIsClosed($model->is_closed)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        return $result;
    }
}