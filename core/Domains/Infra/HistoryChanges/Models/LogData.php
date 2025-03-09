<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Models;

use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Illuminate\Contracts\Support\Arrayable;

class LogData implements Arrayable
{
    private const EVENT   = 'event';
    private const CHANGES = 'changes';
    private const TEXT    = 'text';

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
