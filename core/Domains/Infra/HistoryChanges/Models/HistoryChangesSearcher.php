<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Models;

use App\Models\Infra\HistoryChanges;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class HistoryChangesSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setType(?int $type): static
    {
        if ($type) {
            $this->addWhere(HistoryChanges::TYPE, SearcherInterface::EQUALS, $type);
        }

        return $this;
    }

    public function setPrimaryId(?int $primaryId): static
    {
        if ($primaryId) {
            $this->addWhere(HistoryChanges::PRIMARY_ID, SearcherInterface::EQUALS, $primaryId);
        }

        return $this;
    }

    public function setReferenceType(?int $referenceType): static
    {
        if ($referenceType) {
            $this->addWhere(HistoryChanges::REFERENCE_TYPE, SearcherInterface::EQUALS, $referenceType);
        }

        return $this;
    }

    public function setReferenceId(?int $referenceId): static
    {
        if ($referenceId) {
            $this->addWhere(HistoryChanges::REFERENCE_ID, SearcherInterface::EQUALS, $referenceId);
        }

        return $this;
    }

    public function setMainFilters(?int $type, ?int $primaryId, ?int $referenceType, ?int $referenceId): static
    {
        if ($type) {
            $this->setType($type);
        }

        if ($primaryId) {
            $this->setPrimaryId($primaryId);
        }

        if ($referenceType) {
            $this->setReferenceType($referenceType);
        }

        if ($referenceId) {
            $this->setReferenceId($referenceId);
        }

        return $this;
    }
} 