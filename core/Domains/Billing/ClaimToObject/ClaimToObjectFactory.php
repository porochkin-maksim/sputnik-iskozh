<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

class ClaimToObjectFactory
{
    public function makeDefault(): ClaimToObjectEntity
    {
        return new ClaimToObjectEntity();
    }
}
