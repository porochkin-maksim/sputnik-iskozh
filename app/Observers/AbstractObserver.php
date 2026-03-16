<?php declare(strict_types=1);

namespace App\Observers;

use App\Models\Interfaces\CastsInterface;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

abstract class AbstractObserver
{
    protected HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    /**
     * Должен возвращать массив [поле_модели => человеческое название]
     */
    abstract protected function getPropertyTitles(): array;

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
                CastsInterface::CAST_DATE     => $value->format('d.m.Y'),
                CastsInterface::CAST_DATETIME => $value->format('d.m.Y H:i'),
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