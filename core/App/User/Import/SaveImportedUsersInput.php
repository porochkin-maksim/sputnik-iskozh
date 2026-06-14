<?php declare(strict_types=1);

namespace Core\App\User\Import;

readonly class SaveImportedUsersInput
{
    /**
     * @param array[] $users  массив данных из JSON-сериализации UserImportItem (см. UserImportItem::jsonSerialize)
     */
    public function __construct(
        public array $users,
    )
    {
    }

    /**
     * @return UserImportData[]
     */
    public function getUsers(): array
    {
        return array_map(
            static fn(array $data) => new UserImportData(
                id                : $data['id'],
                accountNumber     : $data['accountNumber'] ?? '',
                fraction          : $data['fraction'] ?? null,
                fullName          : $data['fullName'] ?? '',
                email             : $data['email'] ?? null,
                phone             : $data['phone'] ?? null,
                membershipDate    : $data['membershipDate'] ?? null,
                membershipDutyInfo: $data['membershipDutyInfo'] ?? null,
                addPhone          : $data['addPhone'] ?? null,
                address           : $data['address'] ?? null,
                postAddress       : $data['postAddress'] ?? null,
                note              : $data['note'] ?? null,
            ),
            $this->users,
        );
    }
}
