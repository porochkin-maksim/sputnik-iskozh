<?php declare(strict_types=1);

namespace Core\Domains\Files;

/**
 * @method static static      from(int|string $value)
 * @method static static|null tryFrom(int|string $value)
 * @method static array       cases()
 */
enum FileTypeEnum: int
{
    case CONSTITUTION     = 1;
    case NEWS             = 3;
    case PAYMENT          = 4;
    case COUNTER_HISTORY  = 5;
    case COUNTER_PASSPORT = 6;
    case TICKET           = 7;
    case TICKET_RESULT    = 8;
}
