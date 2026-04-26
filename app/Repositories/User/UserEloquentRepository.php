<?php declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\Account\AccountToUser;
use App\Models\Infra\UserInfo;
use App\Models\User;
use App\Repositories\Shared\DB\RepositoryTrait;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Domains\User\UserCollection;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserRepositoryInterface;
use Core\Domains\User\UserSearcher;
use Core\Domains\User\UserSearchResponse;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use Illuminate\Database\Eloquent\Builder;
use ReturnTypeWillChange;

class UserEloquentRepository implements UserRepositoryInterface
{
    use RepositoryTrait;

    public function __construct(
        private readonly UserEloquentMapper $mapper,
    )
    {
    }

    protected function modelClass(): string
    {
        return User::class;
    }

    protected function getTable(): string
    {
        return User::TABLE;
    }

    protected function getEmptyCollection(): Collection
    {
        return new UserCollection();
    }

    #[ReturnTypeWillChange]
    /**
     * @return UserSearchResponse
     */
    protected function getEmptySearchResponse(): UserSearchResponse
    {
        return new UserSearchResponse();
    }

    #[ReturnTypeWillChange]
    /**
     * @return UserSearcher
     */
    protected function getEmptySearcher(): SearcherInterface
    {
        return new UserSearcher();
    }

    protected function getMapper(): RepositoryDataMapperInterface
    {
        return $this->mapper;
    }

    public function search(SearcherInterface $searcher): UserSearchResponse
    {
        return $this->searchModels($searcher);
    }

    public function getById(?int $id, bool $withDeleted = false): ?UserEntity
    {
        $searcher = new UserSearcher();
        if ($withDeleted) {
            $searcher->setWithDeleted();
        }

        /** @var User|null $model */
        $model = $this->getModelById($id, $searcher);

        return $model ? $this->mapper->makeEntityFromRepositoryData($model) : null;
    }

    public function getByIds(array $ids): UserSearchResponse
    {
        return $this->search($this->getEmptySearcher()->setIds($ids));
    }

    public function save(UserEntity $user): UserEntity
    {
        /** @var User|null $model */
        $model = $this->getModelById($user->getId());
        $model = $this->mapper->makeRepositoryDataFromEntity($user, $model);
        $model->save();

        $model->roles()->sync($user->getRole()?->getId() ? [$user->getRole()->getId()] : []);

        $accountIds = [];
        if ($user->getAccounts()->first()?->getFraction() !== null) {
            foreach ($user->getAccounts() as $account) {
                $accountIds[$account->getId()] = [
                    AccountToUser::FRACTION   => $account->getFraction(),
                    AccountToUser::OWNER_DATE => $account->getOwnerDate()?->format('Y-m-d'),
                ];
            }
        }
        else {
            $accountIds = $user->getAccountIds();
        }
        $model->accounts()->sync($accountIds);

        $userInfo = UserInfo::where(UserInfo::USER_ID, $model->id)->first();
        if ( ! $userInfo) {
            $userInfo = new UserInfo([UserInfo::USER_ID => $model->id]);
        }
        $userInfo->membership_date      = $user->getMembershipDate();
        $userInfo->membership_duty_info = $user->getMembershipDutyInfo();
        $userInfo->save();

        return $this->mapper->makeEntityFromRepositoryData($model)
            ->setMembershipDate($user->getMembershipDate())
            ->setMembershipDutyInfo($user->getMembershipDutyInfo())
        ;
    }

    protected function adaptFieldName(string $field): string
    {
        if ($field === 'account_sort') {
            return $field;
        }

        return sprintf('%s.%s', $this->getTable(), $field);
    }

    protected function getQuery(Builder $query): Builder
    {
        return $query->select(User::TABLE . '.*')
            ->leftJoin(UserInfo::TABLE, UserInfo::TABLE . '.' . UserInfo::USER_ID, SearcherInterface::EQUALS, User::TABLE . '.' . User::ID)
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DATE, UserInfo::MEMBERSHIP_DATE)
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DUTY_INFO, UserInfo::MEMBERSHIP_DUTY_INFO)
        ;
    }
}
