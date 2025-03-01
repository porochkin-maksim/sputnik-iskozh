<?php declare(strict_types=1);

namespace Core\Domains\Account\Services;

use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Models\AccountComparator;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Account\Models\AccountInfo;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Repositories\AccountRepository;
use Core\Domains\Account\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\Option\Services\OptionService;

readonly class AccountService
{
    public function __construct(
        private AccountFactory        $accountFactory,
        private AccountRepository     $accountRepository,
        private OptionService         $optionService,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(AccountDTO $service): AccountDTO
    {
        $model = $this->accountRepository->getById($service->getId());
        if ($model) {
            $before = $this->accountFactory->makeDtoFromObject($model);
        }
        else {
            $before = new AccountDTO();
        }

        $model   = $this->accountRepository->save($this->accountFactory->makeModelFromDto($service, $model));
        $current = $this->accountFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $service->getId() ? Event::UPDATE : Event::CREATE,
            new AccountComparator($current),
            new AccountComparator($before),
        );

        return $current;
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
        $account->users()->sync($dto->getUsers()?->getIds());

        return $this->accountFactory->makeDtoFromObject($account);
    }

    public function search(AccountSearcher $searcher): SearchResponse
    {
        $response = $this->accountRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new AccountCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->accountFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
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

        $membershipFee        = $membershipFee->getMoney();
        $electricTariff       = $electricTariff->getMoney();
        $garbageCollectionFee = $garbageCollectionFee->getMoney();
        $roadCollectionFee    = $roadCollectionFee->getMoney();

        return new AccountInfo(
            $account,
            $membershipFee,
            $electricTariff,
            $garbageCollectionFee,
            $roadCollectionFee,
        );
    }

    public function deleteById(int $id): bool
    {
        $account = $this->getById($id);

        if ( ! $account) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            new AccountComparator($account),
        );

        return $this->accountRepository->deleteById($id);
    }
}
