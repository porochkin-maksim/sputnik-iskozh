<?php declare(strict_types=1);

namespace Core\Domains\Account\Factories;

use App\Models\Account\Account;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\UserLocator;
use Illuminate\Database\Eloquent\Collection;

readonly class AccountFactory
{
    public function makeDefault(): AccountDTO
    {
        return (new AccountDTO())
            ->setBalance(0.00)
            ->setIsVerified(false);
    }

    public function makeModelFromDto(AccountDTO $dto, ?Account $model = null): Account
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Account::make();
        }

        return $result->fill([
            Account::NUMBER          => $dto->getNumber(),
            Account::SIZE            => $dto->getSize(),
            Account::BALANCE         => $dto->getBalance(),
            Account::IS_VERIFIED     => $dto->isVerified(),
            Account::PRIMARY_USER_ID => $dto->getPrimaryUserId(),
            Account::IS_MEMBER       => $dto->isMember(),
            Account::IS_MANAGER      => $dto->isManager(),
        ]);
    }

    public function makeDtoFromObject(Account $model): AccountDTO
    {
        $result = new AccountDTO();

        $result
            ->setId($model->id)
            ->setNumber($model->number)
            ->setSize($model->size)
            ->setBalance($model->balance)
            ->setIsVerified($model->is_verified)
            ->setPrimaryUserId($model->primary_user_id)
            ->setIsMember($model->is_member)
            ->setIsManager($model->is_manager)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[Account::USERS])) {
            foreach ($model->getRelation(Account::USERS) as $user) {
                $result->addUser(UserLocator::UserFactory()->makeDtoFromObject($user));
            }
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): AccountCollection
    {
        $result = new AccountCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }
}
