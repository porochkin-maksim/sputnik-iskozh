<?php declare(strict_types=1);

namespace Core\Objects\News\Models;

use Core\Enums\DateTimeFormat;

readonly class Dossier implements \JsonSerializable
{
    public function __construct(
        private NewsDTO $report
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'updatedAt'   => $this->report->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            'publishedAt' => $this->report->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }
}
