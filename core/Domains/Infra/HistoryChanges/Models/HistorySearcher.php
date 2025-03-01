<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Models;

use App\Models\Infra\HistoryChanges;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Infra\HistoryChanges\Enums\Type;

class HistorySearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this->with[] = HistoryChanges::USER;
    }

    public function setMainFilters(Type|int $type, int $primaryId, ?int $referenceId): static
    {
        $this
            ->addWhere(HistoryChanges::TYPE, SearcherInterface::EQUALS, $type)
            ->addWhere(HistoryChanges::PRIMARY_ID, SearcherInterface::EQUALS, $primaryId)
            ->addWhere(HistoryChanges::REFERENCE_ID, SearcherInterface::EQUALS, $referenceId)
        ;

        return $this;
    }
}
