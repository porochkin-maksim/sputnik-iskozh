<?php declare(strict_types=1);

namespace Core\Domains\User\Services;

use App\Models\Account\Account;
use App\Models\Account\AccountToUser;
use App\Models\Infra\UserInfo;
use App\Models\User;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Enums\UserIdEnum;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserComparator;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Repositories\UserRepository;
use Core\Domains\User\Responses\SearchResponse;

readonly class UserService
{
    public function __construct(
        private UserFactory           $userFactory,
        private UserRepository        $userRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(UserDTO $user): UserDTO
    {
        $model = $this->userRepository->getById($user->getId());

        if ($model) {
            $before = $this->userFactory->makeDtoFromObject($model);
        }
        else {
            $before = new UserDTO();
        }

        $model = $this->userRepository->save($this->userFactory->makeModelFromDto($user, $model));

        $userInfo = UserInfo::where(UserInfo::USER_ID, $user->getId())->first();
        if ( ! $userInfo) {
            $userInfo = new UserInfo([UserInfo::USER_ID => $model->id]);
        }
        $userInfo->membership_date      = $user->getMembershipDate();
        $userInfo->membership_duty_info = $user->getMembershipDutyInfo();
        $userInfo->save();

        $current = $this->userFactory->makeDtoFromObject($model)
            ->setMembershipDate($user->getMembershipDate())
            ->setMembershipDutyInfo($user->getMembershipDutyInfo())
        ;

        $model->roles()->sync($user->getRole()?->getId() ? [$user->getRole()?->getId()] : []);
        $accountIds = [];
        if ($user->getAccounts()->first()?->getFraction() !== null) {
            foreach ($user->getAccounts() as $account) {
                $accountIds[$account->getId()] = [AccountToUser::FRACTION => $account->getFraction()];
            }

        }else{
            $accountIds = $user->getAccountIds();
        }
        $model->accounts()->sync($accountIds);

        $current = $this->getById($current->getId());

        $this->historyChangesService->writeToHistory(
            $user->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::USER,
            $current->getId(),
            null,
            null,
            new UserComparator($current),
            new UserComparator($before),
        );

        return $current;
    }

    public function search(UserSearcher $searcher): SearchResponse
    {
        $response = $this->userRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new UserCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->userFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id, bool $withDeleted = false): ?UserDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new UserSearcher()
            ->setId($id)
            ->setWithDeleted($withDeleted)
        ;

        $result = $this->userRepository->search($searcher)->getItems()->first();

        return $result ? $this->userFactory->makeDtoFromObject($result) : null;
    }

    public function getByEmail(?string $email): ?UserDTO
    {
        if ( ! $email) {
            return null;
        }

        $searcher = new UserSearcher();
        $searcher
            ->addWhere(User::EMAIL, SearcherInterface::EQUALS, $email)
            ->setLimit(1)
        ;
        $result = $this->userRepository->search($searcher)->getItems()->first();

        return $result ? $this->userFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $user = $this->getById($id);

        if ( ! $user || UserIdEnum::OWNER === $id) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::USER,
            $user->getId(),
        );

        return $this->userRepository->deleteById($id);
    }
}
