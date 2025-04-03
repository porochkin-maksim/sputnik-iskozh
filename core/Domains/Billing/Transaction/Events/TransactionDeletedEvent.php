<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Events;

use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public TransactionDTO $transaction)
    {
    }
}
