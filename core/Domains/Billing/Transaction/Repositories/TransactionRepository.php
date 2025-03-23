<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Repositories;

use App\Models\Billing\Transaction;
use Core\Db\RepositoryTrait;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;

class TransactionRepository
{
    private const TABLE = Transaction::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Transaction::class;
    }

    public function getById(?int $id): ?Transaction
    {
        /** @var ?Transaction $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function save(Transaction $transaction): Transaction
    {
        $transaction->save();

        return $transaction;
    }
}
