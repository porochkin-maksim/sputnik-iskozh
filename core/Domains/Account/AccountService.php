<?php declare(strict_types=1);

namespace Core\Domains\Account;

readonly class AccountService
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    )
    {
    }

    public function save(AccountEntity $account): AccountEntity
    {
        return $this->accountRepository->save($account);
    }

    public function getByUserId(int|string|null $id): AccountCollection
    {
        return $this->accountRepository->getByUserId((int) $id);
    }

    public function register(AccountEntity $entity): AccountEntity
    {
        return $this->accountRepository->save($entity);
    }

    public function findByNumber(string $number): ?AccountEntity
    {
        $searcher = new AccountSearcher();
        $searcher->setNumber($number);
        return $this->search($searcher)->getItems()->first();
    }

    public function search(?AccountSearcher $searcher = null): AccountSearchResponse
    {
        return $this->accountRepository->search($searcher ?: new AccountSearcher());
    }

    public function getById(int|string|null $id): ?AccountEntity
    {
        return $this->accountRepository->getById((int) $id);
    }

    public function getByIds(array $ids): AccountCollection
    {
        return $this->accountRepository->getByIds($ids);
    }

    public function getSntAccount(): ?AccountEntity
    {
        return $this->getById(AccountIdEnum::SNT->value);
    }

    public function deleteById(int $id): bool
    {
        if ($id === AccountIdEnum::SNT->value) {
            abort(403);
        }
        $account = $this->getById($id);
        if ( ! $account) {
            return false;
        }
        return $this->accountRepository->deleteById($id);
    }
}
