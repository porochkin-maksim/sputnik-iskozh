<?php declare(strict_types=1);

namespace App\Observers\Files;

use App\Models\Files\FileModel;
use App\Observers\AbstractObserver;
use Core\Domains\HistoryChanges\HistoryType;

class FileObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::FILE;
    }

    protected function getPropertyTitles(): array
    {
        return FileModel::PROPERTIES_TO_TITLES;
    }
}
