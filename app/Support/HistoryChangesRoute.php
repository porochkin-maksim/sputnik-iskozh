<?php declare(strict_types=1);

namespace App\Support;

use App\Resources\RouteNames;
use Core\Domains\HistoryChanges\HistoryType;

final class HistoryChangesRoute
{
    public static function make(
        ?HistoryType $type = null,
        ?int $primaryId = null,
        ?HistoryType $referenceType = null,
        ?int $referenceId = null,
    ): string
    {
        return route(RouteNames::HISTORY_CHANGES, [
            'type' => $type?->value,
            'primary_id' => $primaryId,
            'reference_type' => $referenceType?->value,
            'reference_id' => $referenceId,
        ]);
    }
}
