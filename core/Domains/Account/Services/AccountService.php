<?php declare(strict_types=1);

namespace Core\Domains\Account\Services;

use Core\Domains\Account\Collections\Accounts;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Repositories\AccountRepository;

readonly class AccountService
{
    public function __construct(
        private AccountFactory    $accountFactory,
        private AccountRepository $accountRepository,
    )
    {
    }

    public function getByUserId(int|string|null $id): ?AccountDTO
    {
        $result = $this->accountRepository->getByUserId((int) $id);

        return $result ? $this->accountFactory->makeDtoFromObject($result) : null;
    }

    public function register(AccountDTO $dto): AccountDTO
    {
        $account = $this->accountFactory->makeModelFromDto($dto);
        $account = $this->accountRepository->save($account);
        $account->users()->sync($dto->getUsers()->getIds());

        return $this->accountFactory->makeDtoFromObject($account);
    }

    public function search(AccountSearcher $searcher): Accounts
    {
        $accounts = $this->accountRepository->search($searcher);

        $result  = new Accounts();
        foreach ($accounts as $account) {
            $result->add($this->accountFactory->makeDtoFromObject($account));
        }

        return $result;
    }

    public function getById(int $id): ?AccountDTO
    {
        $result = $this->accountRepository->getById($id);

        return $result ? $this->accountFactory->makeDtoFromObject($result) : null;
    }
}
