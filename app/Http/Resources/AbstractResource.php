<?php declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use JsonSerializable;

abstract readonly class AbstractResource implements JsonSerializable
{
    protected function formatDateTimeOrNow(?Carbon $date): string
    {
        return $date ? $date->format(DateTimeFormat::DATE_TIME_FRONT) : Carbon::now()->format(DateTimeFormat::DATE_TIME_FRONT);
    }

    protected function formatTimestampAt(?Carbon $date): string
    {
        return $date ? $date->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT) : '';
    }
}
