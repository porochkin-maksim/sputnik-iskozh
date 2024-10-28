<?php declare(strict_types=1);

namespace Core\Domains\News\Models;

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
            'createdAt'   => $this->report->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            'publishedAt' => $this->report->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }
}
