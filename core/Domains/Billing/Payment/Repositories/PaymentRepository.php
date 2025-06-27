<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Repositories;

use App\Models\Account\Account;
use App\Models\Billing\Payment;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Database\Eloquent\Builder;

class PaymentRepository
{
    private const string TABLE = Payment::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
        getQuery as traitGetQuery;
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

    private function getQuery(Builder $query): Builder
    {
        $query->select(self::TABLE. '.*')
            ->leftJoin(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, Payment::TABLE . '.' . Payment::ACCOUNT_ID)
            ->selectSub(Account::TABLE . '.' . Account::NUMBER, 'account_number')
        ;

        return $query;
    }
}
