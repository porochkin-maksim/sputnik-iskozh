<?php declare(strict_types=1);

namespace Core\Objects\File\Models;

use Core\Enums\DateTimeFormat;
use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Models\ReportDTO;
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
