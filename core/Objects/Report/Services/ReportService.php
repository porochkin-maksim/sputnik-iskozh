<?php declare(strict_types=1);

namespace Core\Objects\Report\Services;

use Core\Objects\Report\Collections\Reports;
use Core\Objects\Report\Factories\ReportFactory;
use Core\Objects\Report\Models\ReportDTO;
use Core\Objects\Report\Models\ReportSearcher;
use Core\Objects\Report\Repositories\ReportRepository;

readonly class ReportService
{
    public function __construct(
        private ReportFactory    $reportFactory,
        private ReportRepository $reportRepository,
    )
    {
    }

    public function save(ReportDTO $dto): ReportDTO
    {
        $report = null;

        if ($dto->getId()) {
            $report = $this->reportRepository->getById($dto->getId());
        }

        $report = $this->reportFactory->makeModelFromDto($dto, $report);
        $report = $this->reportRepository->save($report);

        return $this->reportFactory->makeDtoFromObject($report);
    }

    public function search(ReportSearcher $searcher): Reports
    {
        $reports = $this->reportRepository->search($searcher);

        $result  = new Reports();
        foreach ($reports as $report) {
            $result->add($this->reportFactory->makeDtoFromObject($report));
        }

        return $result;
    }

    public function getById(int $id): ?ReportDTO
    {
        $result = $this->reportRepository->getById($id);

        return $result ? $this->reportFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        return $this->reportRepository->deleteById($id);
    }
}
