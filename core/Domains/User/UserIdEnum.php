<?php declare(strict_types=1);

namespace Core\Domains\User;

abstract class UserIdEnum
{
    public const null UNDEFINED = null;
    public const int  ROBOT          = 0;
    public const int  OWNER     = 1;
}
