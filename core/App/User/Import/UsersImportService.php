<?php declare(strict_types=1);

namespace Core\App\User\Import;

use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Shared\Helpers\Phone\PhoneHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;

readonly class UsersImportService
{
    public function __construct(
        private UserService    $userService,
        private AccountService $accountService,
    )
    {
    }

    private const int COL_ID              = 0;
    private const int COL_ACCOUNT         = 1;
    private const int COL_FRACTION        = 2;
    private const int COL_FULL_NAME       = 3;
    private const int COL_EMAIL           = 4;
    private const int COL_PHONE           = 5;
    private const int COL_MEMBERSHIP_DATE = 6;
    private const int COL_MEMBERSHIP_DUTY = 7;
    private const int COL_ADD_PHONE       = 8;
    private const int COL_ADDRESS         = 9;
    private const int COL_POST_ADDRESS    = 10;
    private const int COL_NOTE            = 11;

    /**
     * @return UserImportItem[]
     */
    public function parseFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = $worksheet->toArray();

        $usersData = $this->extractRows($rows);
        $usersData = $this->mergeDuplicates($usersData);

        return $this->buildItems($usersData);
    }

    /**
     * @return UserImportData[]
     */
    private function extractRows(array $rows): array
    {
        $result = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $id          = $this->parseInt($row[self::COL_ID] ?? null);
            $accountStr  = trim((string) ($row[self::COL_ACCOUNT] ?? ''));
            $fractionRaw = trim((string) ($row[self::COL_FRACTION] ?? ''));

            if ($accountStr === '' && $id === null) {
                continue;
            }

            $phoneRaw = trim((string) ($row[self::COL_PHONE] ?? ''));
            $phone    = $phoneRaw !== '' ? PhoneHelper::normalizePhone($phoneRaw) : null;

            $result[] = new UserImportData(
                id                : $id,
                accountNumber     : $accountStr,
                fraction          : $this->parseFraction($fractionRaw),
                fullName          : trim((string) ($row[self::COL_FULL_NAME] ?? '')),
                email             : trim((string) ($row[self::COL_EMAIL] ?? '')) ?: null,
                phone             : $phone,
                membershipDate    : trim((string) ($row[self::COL_MEMBERSHIP_DATE] ?? '')) ?: null,
                membershipDutyInfo: trim((string) ($row[self::COL_MEMBERSHIP_DUTY] ?? '')) ?: null,
                addPhone          : trim((string) ($row[self::COL_ADD_PHONE] ?? '')) ?: null,
                address           : trim((string) ($row[self::COL_ADDRESS] ?? '')) ?: null,
                postAddress       : trim((string) ($row[self::COL_POST_ADDRESS] ?? '')) ?: null,
                note              : trim((string) ($row[self::COL_NOTE] ?? '')) ?: null,
            );
        }

        return $result;
    }

    /**
     * @param UserImportData[] $usersData
     * @return UserImportData[]
     */
    private function mergeDuplicates(array $usersData): array
    {
        /** @var array<int, array{data: UserImportData, accounts: AccountItem[]}> $grouped */
        $grouped = [];

        foreach ($usersData as $data) {
            if ($data->id !== null) {
                if ( ! isset($grouped[$data->id])) {
                    $grouped[$data->id] = [
                        'data'     => $data,
                        'accounts' => [],
                    ];
                }

                $grouped[$data->id]['accounts'][] = new AccountItem(
                    number  : $data->accountNumber,
                    fraction: $data->fraction,
                );
            }
            else {
                $grouped[] = ['data' => $data, 'accounts' => []];
            }
        }

        return array_map(
            static fn(array $entry) => new UserImportData(
                id                : $entry['data']->id,
                accountNumber     : $entry['data']->accountNumber,
                fraction          : $entry['data']->fraction,
                fullName          : $entry['data']->fullName,
                email             : $entry['data']->email,
                phone             : $entry['data']->phone,
                membershipDate    : $entry['data']->membershipDate,
                membershipDutyInfo: $entry['data']->membershipDutyInfo,
                addPhone          : $entry['data']->addPhone,
                address           : $entry['data']->address,
                postAddress       : $entry['data']->postAddress,
                note              : $entry['data']->note,
                accounts          : $entry['accounts'],
            ),
            array_values($grouped),
        );
    }

    /**
     * @param UserImportData[] $usersData
     * @return UserImportItem[]
     */
    private function buildItems(array $usersData): array
    {
        $items = [];

        foreach ($usersData as $data) {
            $dbUser       = null;
            $accountError = null;

            if ($data->id !== null) {
                $dbUser = $this->userService->getById($data->id, true);
                if ( ! $dbUser) {
                    continue;
                }
            }
            else {
                if (empty($data->email)) {
                    continue;
                }

                $dbUser = $this->userService->getByEmail($data->email);
            }
            if ($data->accountNumber !== '') {
                $account = $this->findAccountByNumber($data->accountNumber);
                if ( ! $account) {
                    $accountError = "Участок {$data->accountNumber} не найден";
                }
            }

            $dbData = $dbUser ? $this->userToDbData($dbUser) : null;

            $items[] = new UserImportItem(
                importData  : $data,
                dbData      : $dbData,
                accountError: $accountError,
            );
        }

        return $items;
    }

    private function userToDbData(UserEntity $user): UserImportData
    {
        $accounts     = $user->getAccounts();
        $exData       = $user->getExData();
        $viewer       = $user->getViewer();
        $firstAccount = $accounts->count() ? $accounts->first() : null;

        return new UserImportData(
            id                : $user->getId(),
            accountNumber     : $firstAccount?->getNumber() ?? '',
            fraction          : $firstAccount?->getFraction() ?: null,
            fullName          : $viewer->getFullName(),
            email             : $user->getEmail(),
            phone             : $user->getPhone() ? PhoneHelper::normalizePhone($user->getPhone()) : null,
            membershipDate    : $user->getMembershipDate()?->format('d.m.Y'),
            membershipDutyInfo: $user->getMembershipDutyInfo(),
            addPhone          : $exData?->getPhone(),
            address           : $exData?->getLegalAddress(),
            postAddress       : $exData?->getPostAddress(),
            note              : $exData?->getAdditional(),
        );
    }

    private function parseInt(mixed $value): ?int
    {
        if (in_array($value, [null, '', 0, '0'], true)) {
            return null;
        }

        return (int) $value;
    }

    private function parseFraction(?string $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $clean = str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $value));
        $float = (float) $clean;
        if ($float > 1) {
            $float /= 100;
        }

        return $float;
    }

    private function findAccountByNumber(string $number): ?AccountEntity
    {
        return $this->accountService->findByNumber($number);
    }
}
