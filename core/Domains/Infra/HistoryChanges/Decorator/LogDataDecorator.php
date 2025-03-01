<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Decorator;

use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Models\LogData;

readonly class LogDataDecorator
{
    public function __construct(
        private LogData $logData,
    )
    {
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getChanges(): array
    {
        $result = [];

        if ( ! $this->logData->getChanges()) {
            return $result;
        }

        foreach ($this->logData->getChanges()->getIterator() as $item) {
            $result[] = new Changes(
                $this->getTitle($item->getTitle()),
                $this->geOldValue($item->getOldValue()),
                $this->getNewValue($item->getNewValue()),
            );
        }

        return $result;
    }

    public function getActionEvent(): string
    {
        return match ($this->logData->getEvent()) {
            Event::CREATE => 'Создана запись',
            Event::UPDATE => 'Обновлена запись',
            Event::DELETE => 'Удалена запись',
        };
    }

    private function getTitle(string $title): string
    {
        return $title;
    }

    private function geOldValue(string $value): string
    {
        return $value;
    }

    private function getNewValue(string $value): string
    {
        return $value;
    }
}
