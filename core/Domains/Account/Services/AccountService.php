<?php declare(strict_types=1);

namespace Core\Domains\Account\Services;

use Core\Domains\Account\Collections\Accounts;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Account\Models\AccountInfo;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Repositories\AccountRepository;
use Core\Domains\Option\Services\OptionService;
use Core\Services\Money\MoneyService;

readonly class AccountService
{
    public function __construct(
        private AccountFactory    $accountFactory,
        private AccountRepository $accountRepository,
        private OptionService     $optionService,
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

        $result = new Accounts();
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

    public function getAccountInfo(AccountDTO $account): AccountInfo
    {
        $electricTariff       = $this->optionService->getElectricTariff();
        $membershipFee        = $this->optionService->getMembershipFee();
        $garbageCollectionFee = $this->optionService->getGarbageCollectionFee();
        $roadCollectionFee    = $this->optionService->getRoadCollectionFee();

        $membershipFee        = MoneyService::parse($membershipFee->getData());
        $electricTariff       = MoneyService::parse($electricTariff->getData());
        $garbageCollectionFee = MoneyService::parse($garbageCollectionFee->getData());
        $roadCollectionFee    = MoneyService::parse($roadCollectionFee->getData());

        return new AccountInfo(
            $account,
            $membershipFee,
            $electricTariff,
            $garbageCollectionFee,
            $roadCollectionFee,
        );
    }
}
