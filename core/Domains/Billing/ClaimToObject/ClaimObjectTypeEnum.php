<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

enum ClaimObjectTypeEnum: int
{
    case COUNTER_HISTORY = 1;
}
