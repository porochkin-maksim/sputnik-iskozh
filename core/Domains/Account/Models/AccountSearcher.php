<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use App\Models\Account\Account;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;
use Core\Domains\Account\Enums\AccountIdEnum;

class AccountSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function __construct()
    {
        $this->select = [
            Account::ID,
            Account::NUMBER,
            Account::SIZE,
            Account::BALANCE,
            Account::IS_VERIFIED,
            Account::PRIMARY_USER_ID,
            Account::IS_INVOICING,
            Account::IS_MANAGER,
        ];
    }

    public function setNumber(string $number): static
    {
        $this->addWhere(Account::NUMBER, SearcherInterface::EQUALS, $number);

        return $this;
    }

    public function setWithoutSntAccount(): static
    {
        $this->addWhere(Account::ID, SearcherInterface::IS_NOT, AccountIdEnum::SNT->value);

        return $this;
    }

    public function setWithUsers(): static
    {
        $this->with[] = Account::USERS;

        return $this;
    }
}
