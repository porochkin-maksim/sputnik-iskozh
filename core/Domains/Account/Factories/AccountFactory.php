<?php declare(strict_types=1);

namespace Core\Domains\Account\Factories;

use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\UserLocator;

readonly class AccountFactory
{
    public function makeModelFromDto(AccountDTO $dto, ?Account $account = null): Account
    {
        if ($account) {
            $result = $account;
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

    public function makeDtoFromObject(Account $account): AccountDTO
    {
        $result = new AccountDTO();

        $result
            ->setId($account->id)
            ->setNumber($account->number)
            ->setSize($account->size)
            ->setPrimaryUserId($account->primary_user_id)
            ->setIsMember($account->is_member)
            ->setIsManager($account->is_manager)
            ->setCreatedAt($account->created_at)
            ->setUpdatedAt($account->updated_at);

        foreach ($account->users ?? [] as $user) {
            $result->addUser(UserLocator::UserFactory()->makeDtoFromObject($user));
        }

        return $result;
    }
}