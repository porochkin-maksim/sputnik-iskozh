<?php declare(strict_types=1);

namespace Core\Domains\Account;

readonly class AccountFactory
{
    public function makeDefault(): AccountEntity
    {
        return (new AccountEntity())
            ->setNumber(null)
            ->setSize(null)
            ->setBalance(0)
            ->setIsVerified(false)
            ->setIsInvoicing(false)
            ->setSortValue(null)
            ->setExData(new AccountExDataEntity());
    }
}
