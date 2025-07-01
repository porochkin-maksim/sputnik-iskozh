<?php declare(strict_types=1);

namespace Core\Domains\User\Repositories;

use App\Models\Account\Account;
use App\Models\Account\AccountToUser;
use App\Models\Infra\UserInfo;
use App\Models\User;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\User\Collections\UserCollection;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    private const string TABLE = User::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
        getQuery as traitGetQuery;
        adaptFieldName as traitAdaptFieldName;
    }

    protected function modelClass(): string
    {
        return User::class;
    }

    public function getById(?int $id): ?User
    {
        /** @var ?User $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    public function getByIds(array $ids): UserCollection
    {
        return new UserCollection($this->traitGetByIds($ids));
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
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
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::OWNERSHIP_DATE, UserInfo::OWNERSHIP_DATE)
            ->selectSub(UserInfo::TABLE . '.' . UserInfo::OWNERSHIP_DUTY_INFO, UserInfo::OWNERSHIP_DUTY_INFO)

            ->leftJoin(AccountToUser::TABLE, AccountToUser::TABLE . '.' . AccountToUser::USER, SearcherInterface::EQUALS, User::TABLE . '.' . User::ID)
            ->leftJoin(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, AccountToUser::TABLE . '.' . AccountToUser::ACCOUNT)
            ->selectSub(Account::TABLE . '.' . Account::SORT_VALUE, 'account_sort')
        ;
        $query->toRawSql();

        return $query;
    }
}
