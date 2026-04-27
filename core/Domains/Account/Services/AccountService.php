<?php declare(strict_types=1);

namespace Core\Domains\Account\Services;

use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Enums\AccountIdEnum;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Repositories\AccountRepository;
use Core\Domains\Account\Responses\AccountSearchResponse;

readonly class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
    )
    {
    }

    public function save(AccountDTO $account): AccountDTO
    {
        return $this->accountRepository->save($account);
    }

    public function getByUserId(int|string|null $id): AccountCollection
    {
        return $this->accountRepository->getByUserId((int) $id);
    }

    public function register(AccountDTO $dto): AccountDTO
    {
        return $this->accountRepository->save($dto);
    }

    public function findByNumber(string $number): ?AccountDTO
    {
        $searcher = new AccountSearcher();
        $searcher->setNumber($number);

        return $this->search($searcher)->getItems()->first();
    }

    public function search(?AccountSearcher $searcher = null): AccountSearchResponse
    {
        return $this->accountRepository->search($searcher ?: new AccountSearcher());
    }

    public function getById(int|string|null $id): ?AccountDTO
    {
        return $this->accountRepository->getById((int) $id);
    }

    public function getByIds(array $ids): AccountCollection
    {
        return $this->accountRepository->getByIds($ids);
    }

    public function getSntAccount(): ?AccountDTO
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
