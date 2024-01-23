<?php declare(strict_types=1);

namespace Core\Objects\User\Repositories;

use Core\Cache\AbstractCacheRepository;
use Core\Cache\CachePrefixEnum;

class UserCacheRepository extends AbstractCacheRepository
{
    protected function prefix(): CachePrefixEnum
    {
        return CachePrefixEnum::User;
    }
}
