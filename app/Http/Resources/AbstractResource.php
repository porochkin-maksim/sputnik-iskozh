<?php declare(strict_types=1);

namespace App\Http\Resources;

use Carbon\Carbon;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use JsonSerializable;

abstract readonly class AbstractResource implements JsonSerializable
{
    protected function formatDateTimeOrNowForFront(?Carbon $date): string
    {
        return ($date ? : Carbon::now())->format(DateTimeFormat::DATE_TIME_MAIN);
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
