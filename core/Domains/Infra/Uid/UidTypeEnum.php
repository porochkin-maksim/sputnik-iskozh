<?php declare(strict_types=1);

namespace Core\Domains\Infra\Uid;

enum UidTypeEnum: int
{
    case COUNTER = 1;
    case INVOICE = 2;
}
