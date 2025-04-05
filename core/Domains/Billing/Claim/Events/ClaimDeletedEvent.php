<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Events;

use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClaimDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public ClaimDTO $claim)
    {
    }
}
