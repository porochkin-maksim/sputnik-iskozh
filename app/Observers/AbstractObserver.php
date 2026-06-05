<?php declare(strict_types=1);

namespace App\Observers;

use App\Models\Interfaces\CastsInterface;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Shared\Helpers\DateTime\DateTimeHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

abstract class AbstractObserver
{
    protected HistoryChangesService $historyChangesService;

    public function __construct(HistoryChangesService $historyChangesService)
    {
        $this->historyChangesService = $historyChangesService;
    }

    /**
     * Должен возвращать массив [поле_модели => человеческое название]
     */
    abstract protected function getPropertyTitles(): array;

    abstract protected function getPrimaryHistoryType(): HistoryType;

    protected function getPrimaryIdField(): ?string
    {
        return 'id';
    }

    protected function getReferenceHistoryType(): ?HistoryType
    {
        return null;
    }

    protected function getReferenceIdField(): ?string
    {
        return null;
    }

    public function created(Model $item): void
    {
        $changes = $this->makeChanges($item);

        if ( ! $changes->count()) {
            return;
        }

        $primaryId   = $this->getPrimaryIdField() ? $item->{$this->getPrimaryIdField()} : null;
        $referenceId = $this->getReferenceIdField() ? $item->{$this->getReferenceIdField()} : null;

        $this->historyChangesService->logChanges(
            Event::CREATE,
            $this->getPrimaryHistoryType(),
            $changes,
            $primaryId,
            $this->getReferenceHistoryType(),
            $referenceId,
        );
    }

    public function updated(Model $item): void
    {
        $changes = $this->makeChanges($item);

        if ( ! $changes->count()) {
            return;
        }

        $primaryId   = $this->getPrimaryIdField() ? $item->{$this->getPrimaryIdField()} : null;
        $referenceId = $this->getReferenceIdField() ? $item->{$this->getReferenceIdField()} : null;

        $this->historyChangesService->logChanges(
            Event::UPDATE,
            $this->getPrimaryHistoryType(),
            $changes,
            $primaryId,
            $this->getReferenceHistoryType(),
            $referenceId,
        );
    }

    public function deleted(Model $item): void
    {
        $primaryId   = $this->getPrimaryIdField() ? $item->{$this->getPrimaryIdField()} : null;
        $referenceId = $this->getReferenceIdField() ? $item->{$this->getReferenceIdField()} : null;

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            $this->getPrimaryHistoryType(),
            $primaryId,
            $this->getReferenceHistoryType(),
            $referenceId,
        );
    }

    public function forceDeleted(Model $item): void
    {
        // $this->deleted($item);
    }

    public function restored(Model $item): void
    {
        //
    }

    /**
     * Формирует коллекцию изменений
     */
    protected function makeChanges(Model $model): ChangesCollection
    {
        $titles  = $this->getPropertyTitles();
        $changes = new ChangesCollection();

        foreach ($model->getChanges() as $field => $newValue) {
            if ( ! isset($titles[$field])) {
                continue;
            }

            $oldValue = $model->getOriginal($field);
            $oldStr   = $this->formatValue($oldValue, $field, $model);
            $newStr   = $this->formatValue($newValue, $field, $model);

            $changes->add(new Changes($titles[$field], $oldStr, $newStr));
        }

        return $changes;
    }

    /**
     * Форматирует значение для читаемого отображения
     */
    protected function formatValue($value, string $field, Model $model): string
    {
        if (is_null($value)) {
            return 'не указано';
        }

        if ($model instanceof CastsInterface) {
            $value = match ($model->getCasts()[$field] ?? null) {
                CastsInterface::CAST_DATE     => DateTimeHelper::toCarbonOrNull($value)?->format('d.m.Y'),
                CastsInterface::CAST_DATETIME => DateTimeHelper::toCarbonOrNull($value)?->format('d.m.Y H:i'),
                CastsInterface::CAST_BOOLEAN  => (bool) $value,
                default                       => $value,
            };
        }

        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if ($value instanceof Carbon) {
            return $value->format('d.m.Y H:i');
        }

        if (is_object($value) && method_exists($value, 'toDateTimeString')) {
            return $value->toDateTimeString();
        }

        return (string) $value;
    }
}
