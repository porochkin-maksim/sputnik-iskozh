<?php declare(strict_types=1);

namespace Core\Domains\Account\Factories;

use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\UserLocator;

readonly class AccountFactory
{
    public function makeDefault(): AccountDTO
    {
        return new AccountDTO();
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
            Account::PRIMARY_USER_ID => $dto->getPrimaryUserId(),
            Account::IS_MEMBER       => $dto->getIsMember(),
            Account::IS_MANAGER      => $dto->getIsManager(),
        ]);
    }

    public function makeDtoFromObject(Account $model): AccountDTO
    {
        $result = new AccountDTO();

        $result
            ->setId($model->id)
            ->setNumber($model->number)
            ->setSize($model->size)
            ->setPrimaryUserId($model->primary_user_id)
            ->setIsMember($model->is_member)
            ->setIsManager($model->is_manager)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        foreach ($model->users ?? [] as $user) {
            $result->addUser(UserLocator::UserFactory()->makeDtoFromObject($user));
        }

        return $result;
    }
}