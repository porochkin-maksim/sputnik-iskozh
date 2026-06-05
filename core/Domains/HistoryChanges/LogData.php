<?php declare(strict_types=1);

namespace Core\Domains\HistoryChanges;

use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Contracts\ArrayableInterface;

class LogData implements ArrayableInterface
{
    private const string EVENT   = 'event';
    private const string CHANGES = 'changes';
    private const string TEXT    = 'text';

    public function __construct(
        private readonly Event              $event,
        private readonly ?ChangesCollection $changes,
        private readonly ?string            $text,
    )
    {
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getChanges(): ?ChangesCollection
    {
        return $this->changes;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function toArray(): array
    {
        $result = [
            self::EVENT => $this->event,
            self::TEXT  => $this->text,
        ];

        if ($this->changes) {
            $result[self::CHANGES] = $this->changes->toArray();
        }

        return $result;
    }

    public static function fromArray(array $data): static
    {
        $event   = Event::tryFrom($data[self::EVENT]);
        $changes = array_key_exists(self::CHANGES, $data) ? ChangesCollection::makeFromArray($data[self::CHANGES]) : null;
        $text    = $data[self::TEXT] ?? null;

        return new static($event, $changes, $text);
    }
}
