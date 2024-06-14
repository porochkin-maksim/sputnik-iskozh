<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use Core\Enums\DateTimeFormat;
use Core\Domains\Report\Enums\CategoryEnum;
use Core\Domains\Report\Models\ReportDTO;
use function PHPUnit\Framework\isNull;

readonly class Dossier implements \JsonSerializable
{
    public function __construct(
        private FileDTO $report
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'updatedAt' => $this->report->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }
}
