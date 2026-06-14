<?php declare(strict_types=1);

namespace Core\App\User\Import;

use App\Models\Account\Account;
use Core\App\User\Save\SaveCommand;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\User\UserRepositoryInterface;
use Core\Repositories\SearcherInterface;

readonly class SaveImportedUsersCommand
{
    public function __construct(
        private SaveCommand             $saveCommand,
        private AccountService          $accountService,
        private UserRepositoryInterface $userRepository,
    )
    {
    }

    public function execute(SaveImportedUsersInput $input): void
    {
        foreach ($input->getUsers() as $data) {
            $nameParts = $this->parseFullName($data->fullName);

            $fractions = [];

            if ($data->accountNumber !== '') {
                $account = $this->findAccount($data->accountNumber);
                if ($account) {
                    $fractions[$account->getId()] = $data->fraction;
                }
            }

            foreach ($data->accounts as $accData) {
                $account = $this->findAccount($accData->number);
                if ($account && ! isset($fractions[$account->getId()])) {
                    $fractions[$account->getId()] = $accData->fraction;
                }
            }

            $user = $this->userRepository->getById($data->id) ?: $this->userRepository->getByEmail($data->email);

            $this->saveCommand->execute(
                id                : $user?->getId(),
                firstName         : $nameParts['firstName'],
                middleName        : $nameParts['middleName'],
                lastName          : $nameParts['lastName'],
                email             : $data->email,
                phone             : $data->phone,
                roleId            : $user?->getRole()?->getId(),
                membershipDutyInfo: $data->membershipDutyInfo,
                membershipDate    : $data->membershipDate,
                fractions         : $fractions,
                ownerDates        : [],
                addPhone          : $data->addPhone,
                legalAddress      : $data->address,
                postAddress       : $data->postAddress,
                additional        : $data->note,
            );
        }
    }

    /**
     * @return array{lastName: ?string, firstName: ?string, middleName: ?string}
     */
    private function parseFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));

        return [
            'lastName'   => $parts[0] ?? null,
            'firstName'  => $parts[1] ?? null,
            'middleName' => $parts[2] ?? null,
        ];
    }

    private function findAccount(string $number): ?AccountEntity
    {
        $searcher = new AccountSearcher();
        $searcher->addWhere(Account::NUMBER, SearcherInterface::EQUALS, $number);

        return $this->accountService->search($searcher)->getItems()->first();
    }
}
