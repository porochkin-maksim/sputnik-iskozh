<?php declare(strict_types=1);

namespace Core\Objects\Report\Repositories;

use App\Models\Report;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Objects\Report\Collections\Reports;
use Core\Objects\Report\Models\ReportSearcher;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Report::class;
    }

    public function getById(?int $id): ?Report
    {
        /** @var ?Report $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): Reports
    {
        return new Reports($this->traitGetByIds($ids));
    }

    public function save(Report $report): Report
    {
        $report->version = (int) $report->version;
        $report->version++;

        $report->save();

        return $report;
    }

    /**
     * @return Report[]
     */
    public function search(ReportSearcher $searcher): array
    {
        $ids = DB::table(Report::TABLE)
            ->pluck('id')
            ->toArray();

        return $this->traitGetByIds($ids, $searcher);
    }
}
