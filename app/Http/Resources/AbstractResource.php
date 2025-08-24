<?php declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use JsonSerializable;

abstract readonly class AbstractResource implements JsonSerializable
{
    protected function formatDateTimeOrNowForFront(?Carbon $date): string
    {
        return ($date ? : Carbon::now())->format(DateTimeFormat::DATE_TIME_FRONT);
    }

    protected function formatDateOrNowForFront(?Carbon $date): string
    {
        return ($date ? : Carbon::now())->format(DateTimeFormat::DATE_DEFAULT);
    }

    protected function formatDateTimeForRender(?Carbon $date): string
    {
        return $date ? $date->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT) : '';
    }

    protected function formatDateForRender(?Carbon $date): string
    {
        return $date ? $date->format(DateTimeFormat::DATE_VIEW_FORMAT) : '';
    }
}
