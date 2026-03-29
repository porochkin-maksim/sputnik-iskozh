<?php declare(strict_types=1);

namespace Core\Domains\User\Repositories;

use App\Models\Account\AccountToUser;
use App\Models\Infra\UserInfo;
use App\Models\User;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserDTO;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Responses\UserSearchResponse;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    use RepositoryTrait {
        getQuery as traitGetQuery;
        adaptFieldName as traitAdaptFieldName;
    }

    private const string TABLE = User::TABLE;

    public function __construct(
        private readonly UserFactory $userFactory,
    )
    {
    }

    protected function modelClass(): string
    {
        return User::class;
    }

    public function search(SearcherInterface $searcher): UserSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new UserCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->userFactory->makeDtoFromObject($model));
        }

        $result = new UserSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id, bool $withDeleted = false): ?UserDTO
    {
        /** @var null|User $model */
        $searcher = new UserSearcher();
        if ($withDeleted) {
            $searcher->setWithDeleted();
        }

        $model = $this->getModelById($id, $searcher);

        return $model ? $this->userFactory->makeDtoFromObject($model) : null;
    }

    public function save(UserDTO $dto): UserDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->userFactory->makeModelFromDto($dto, $model);
        $model->save();

        $model->roles()->sync($dto->getRole()?->getId() ? [$dto->getRole()?->getId()] : []);
        $accountIds = [];
        if ($dto->getAccounts()->first()?->getFraction() !== null) {
            foreach ($dto->getAccounts() as $account) {
                $accountIds[$account->getId()] = [
                    AccountToUser::FRACTION   => $account->getFraction(),
                    AccountToUser::OWNER_DATE => $account->getOwnerDate()?->format('Y-m-d'),
                ];
            }
        }
        else {
            $accountIds = $dto->getAccountIds();
        }
        $model->accounts()->sync($accountIds);

        $userInfo = UserInfo::where(UserInfo::USER_ID, $dto->getId())->first();
        if ( ! $userInfo) {
            $userInfo = new UserInfo([UserInfo::USER_ID => $model->id]);
        }
        $userInfo->membership_date      = $dto->getMembershipDate();
        $userInfo->membership_duty_info = $dto->getMembershipDutyInfo();
        $userInfo->save();

        return $this->userFactory->makeDtoFromObject($model)
            ->setMembershipDate($dto->getMembershipDate())
            ->setMembershipDutyInfo($dto->getMembershipDutyInfo())
        ;
    }

    private function adaptFieldName(string $field): string
    {
        if ($field === 'account_sort') {
            return $field;
        }

        return $this->traitAdaptFieldName($field);
    }

    private function getQuery(Builder $query): Builder
    {
        $query->select(self::TABLE. '.*')
            ->leftJoin(UserInfo::TABLE, UserInfo::TABLE . '.' . UserInfo::USER_ID, SearcherInterface::EQUALS, User::TABLE . '.' . User::ID)
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DATE, UserInfo::MEMBERSHIP_DATE)
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DUTY_INFO, UserInfo::MEMBERSHIP_DUTY_INFO)
        ;
        $query->toRawSql();

        return $query;
    }
}
