<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges;

use lc;

readonly class HistoryChangesFactory
{
    public function makeDefault(): HistoryChangesEntity
    {
        return (new HistoryChangesEntity())
            ->setId(null)
            ->setType(null)
            ->setReferenceType(null)
            ->setUserId(lc::user()->getId())
            ->setPrimaryId(null)
            ->setReferenceId(null)
            ->setLog(null);
    }
}
