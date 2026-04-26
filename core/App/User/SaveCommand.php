<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountService;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Core\Shared\Helpers\DateTime\DateTimeHelper;
use Core\Shared\Helpers\Phone\PhoneHelper;
use Illuminate\Support\Str;

readonly class SaveCommand
{
    public function __construct(
        private UserFactory    $userFactory,
        private UserService    $userService,
        private AccountService $accountService,
        private RoleService    $roleService,
        private ExDataService  $exDataService,
        private SaveValidator  $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $id,
        ?string $firstName,
        ?string $middleName,
        ?string $lastName,
        ?string $email,
        ?string $phone,
        int     $roleId,
        ?string $membershipDutyInfo,
        mixed   $membershipDate,
        array   $fractions,
        array   $ownerDates,
        ?string $addPhone,
        ?string $legalAddress,
        ?string $postAddress,
        ?string $additional,
    ): ?UserEntity
    {
        $this->validator->validate($id, $email);

        $user = $id
            ? $this->userService->getById($id, true)
            : $this->userFactory->makeDefault()->setPassword(Str::random(8));

        if ($user === null) {
            return null;
        }

        $user->setFirstName($firstName)
            ->setMiddleName($middleName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPhone($phone)
            ->setRole($this->roleService->getById($roleId))
            ->setMembershipDutyInfo($membershipDutyInfo)
            ->setMembershipDate($membershipDate)
        ;

        $accounts = $fractions ? $this->accountService->getByIds(array_keys($fractions)) : new AccountCollection();
        foreach ($accounts as $account) {
            $account->setFraction((float) $fractions[$account->getId()]);
            $account->setOwnerDate(DateTimeHelper::toCarbonOrNull($ownerDates[$account->getId()] ?? null));
        }
        $user->setAccounts($accounts);

        $user = $this->userService->save($user);

        $exData = $this->exDataService->getByTypeAndReferenceId(ExDataTypeEnum::USER, $user->getId())
            ? : $this->exDataService->makeDefault(ExDataTypeEnum::USER)->setReferenceId($user->getId());

        $exData->setData(
            $user->getExData()
                ->setPhone(PhoneHelper::normalizePhone($addPhone))
                ->setLegalAddress($legalAddress)
                ->setPostAddress($postAddress ? : $legalAddress)
                ->setAdditional($additional)
                ->jsonSerialize(),
        );

        $this->exDataService->save($exData);

        return $user;
    }
}
