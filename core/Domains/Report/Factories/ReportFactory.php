<?php declare(strict_types=1);

namespace Core\Domains\Report\Factories;

use App\Models\Report;
use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Domains\File\Factories\FileFactory;
use Core\Domains\Report\Enums\CategoryEnum;
use Core\Domains\Report\Enums\TypeEnum;
use Core\Domains\Report\Models\ReportDTO;

readonly class ReportFactory
{
    public function __construct(
        private FileFactory $fileFactory,
    )
    {
    }

    public function makeDefault(): ReportDTO
    {
        return (new ReportDTO())
            ->setId(null)
            ->setCategory(CategoryEnum::UNDEFINED)
            ->setType(TypeEnum::MONTH)
            ->setYear(Carbon::now()->year)
            ->setStartAt(Carbon::now()->startOfMonth())
            ->setEndAt(Carbon::now()->endOfMonth())
            ->setMoney(null)
            ->setVersion(null)
            ->setParentId(null);
    }

    public function makeModelFromDto(ReportDTO $dto, ?Report $model = null): Report
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Report::make();
        }

        $startAt = $dto->getStartAt()?->format(DateTimeFormat::DATE_TIME_DEFAULT)
            ?: Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        $endAt = $dto->getEndAt()?->format(DateTimeFormat::DATE_TIME_DEFAULT)
            ?: Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        $year = Carbon::parse($startAt)->year;

        return $result->fill([
            Report::NAME      => $dto->getName() ? : $dto->getCategory()?->defaultName(),
            Report::CATEGORY  => $dto->getCategory()?->value,
            Report::TYPE      => $dto->getType()?->value,
            Report::YEAR      => $year,
            Report::START_AT  => $startAt,
            Report::END_AT    => $endAt,
            Report::MONEY     => $dto->getMoney(),
            Report::VERSION   => $dto->getVersion(),
            Report::PARENT_ID => $dto->getParentId(),
        ]);
    }

    public function makeDtoFromObject(Report $model): ReportDTO
    {
        $result = new ReportDTO();

        $result
            ->setId($model->id)
            ->setName($model->name)
            ->setCategory(CategoryEnum::from($model->category))
            ->setType(TypeEnum::from($model->type))
            ->setYear($model->year)
            ->setStartAt($model->start_at)
            ->setEndAt($model->end_at)
            ->setMoney($model->money ? (float) $model->money : null)
            ->setVersion($model->version)
            ->setParentId($model->parent_id)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if ($model->{Report::FILES}) {
            $result->setFiles($this->fileFactory->makeDtoFromObjects($model->{Report::FILES}));
        }

        return $result;
    }
}