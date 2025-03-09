<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Decorator;

use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Core\Domains\Infra\HistoryChanges\Models\LogData;

readonly class HistoryChangesDecorator
{
    private LogData $logData;

    public function __construct(
        private HistoryChangesDTO $historyChanges,
    )
    {
        $this->logData = $this->historyChanges->getLog();
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

    public function getEvent(): Event
    {
        return $this->logData->getEvent();
    }

    public function getActionEventText(): string
    {
        $type = $this->historyChanges->getReferenceType()?->name() ? : $this->historyChanges->getType()?->name();
        $id   = $this->historyChanges->getReferenceId() ? : $this->historyChanges->getPrimaryId();

        return match ($this->logData->getEvent()) {
            Event::CREATE => sprintf('Создана запись «%s» id:%s', $type, $id),
            Event::UPDATE => sprintf('Обновлена запись «%s» id:%s', $type, $id),
            Event::DELETE => sprintf('Удалена запись «%s» id:%s', $type, $id),
            Event::COMMON => (string) $this->logData->getText(),
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
