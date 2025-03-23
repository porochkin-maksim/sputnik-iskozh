<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Repositories;

use App\Models\Billing\Payment;
use Core\Db\RepositoryTrait;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;

class PaymentRepository
{
    private const TABLE = Payment::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Payment::class;
    }

    public function getById(?int $id): ?Payment
    {
        /** @var ?Payment $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Payment $payment): Payment
    {
        $payment->save();

        return $payment;
    }
}
