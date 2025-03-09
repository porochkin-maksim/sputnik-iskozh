<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Models;

use App\Models\Infra\HistoryChanges;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class HistorySearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this->with[] = HistoryChanges::USER;
    }

    public function setMainFilters(
        HistoryType|int|null $type,
        ?int                 $primaryId,
        HistoryType|int|null $referenceType,
        ?int                 $referenceId,
    ): static
    {
        $this->addWhere(HistoryChanges::TYPE, SearcherInterface::EQUALS, $type);

        if ($type) {
            $this->addWhere(HistoryChanges::TYPE, SearcherInterface::EQUALS, $type);
        }

        if ($primaryId) {
            $this->addWhere(HistoryChanges::PRIMARY_ID, SearcherInterface::EQUALS, $primaryId);
        }

        if ($referenceType) {
            $this->addWhere(HistoryChanges::REFERENCE_TYPE, SearcherInterface::EQUALS, $referenceType);
        }

        if ($referenceId) {
            $this->addWhere(HistoryChanges::REFERENCE_ID, SearcherInterface::EQUALS, $referenceId);
        }

        return $this;
    }
}
