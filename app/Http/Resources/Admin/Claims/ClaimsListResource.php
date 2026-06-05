<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Claim\ClaimCollection;

readonly class ClaimsListResource extends AbstractResource
{
    public function __construct(
        private ClaimCollection $claimCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->claimCollection as $claim) {
            $result[] = new ClaimResource($claim);
        }

        return $result;
    }
}
