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
        $result = new ReportDTO();

        return $result
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

    public function makeModelFromDto(ReportDTO $dto, ?Report $report = null): Report
    {
        if ($report) {
            $result = $report;
        }
        else {
            $result = Report::make();
        }

        $startAt = $dto->getStartAt()
            ? $dto->getStartAt()->format(DateTimeFormat::DATE_TIME_DEFAULT)
            : Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        $endAt = $dto->getEndAt()
            ? $dto->getEndAt()->format(DateTimeFormat::DATE_TIME_DEFAULT)
            : Carbon::now()->format(DateTimeFormat::DATE_TIME_DEFAULT);

        $year = Carbon::parse($startAt)->year;

        return $result->fill([
            Report::NAME      => $dto->getName() ? : $dto->getCategory()->defaultName(),
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

    public function makeDtoFromObject(Report $report): ReportDTO
    {
        $result = new ReportDTO();

        $result
            ->setId($report->id)
            ->setName($report->name)
            ->setCategory(CategoryEnum::from($report->category))
            ->setType(TypeEnum::from($report->type))
            ->setYear($report->year)
            ->setStartAt($report->start_at)
            ->setEndAt($report->end_at)
            ->setMoney($report->money ? (float) $report->money : null)
            ->setVersion($report->version)
            ->setParentId($report->parent_id)
            ->setCreatedAt($report->created_at)
            ->setUpdatedAt($report->updated_at);

        if ($report->{Report::FILES}) {
            $result->setFiles($this->fileFactory->makeDtoFromObjects($report->{Report::FILES}));
        }

        return $result;
    }
}