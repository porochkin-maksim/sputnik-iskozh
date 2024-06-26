<?php declare(strict_types=1);

namespace Core\Domains\Report\Models;

use Core\Enums\DateTimeFormat;
use Core\Domains\Report\Enums\CategoryEnum;

readonly class Dossier implements \JsonSerializable
{
    public function __construct(
        private ReportDTO $report
    )
    {
    }

    public function jsonSerialize(): array
    {
        $startAt = $this->report->getStartAt()?->format(DateTimeFormat::DATE_VIEW_FORMAT);
        $endAt   = $this->report->getEndAt()?->format(DateTimeFormat::DATE_VIEW_FORMAT);

        $period = implode(' - ', [$startAt, $endAt]);

        $category = $this->report->getCategory() === CategoryEnum::UNDEFINED ? '' : $this->report->getCategory()?->name();

        $money = $this->report->getMoney();
        if (is_numeric($money)) {
            $money = number_format($money, 2, ',', ' ') . 'руб.';
        }

        return [
            'category'  => $category,
            'type'      => $this->report->getType()?->name(),
            'period'    => $period,
            'updatedAt' => $this->report->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            'money'     => $money,
        ];
    }
}
