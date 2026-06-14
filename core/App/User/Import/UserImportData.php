<?php declare(strict_types=1);

namespace Core\App\User\Import;

readonly class UserImportData
{
    /**
     * @param AccountItem[] $accounts
     */
    public function __construct(
        public ?int    $id,
        public string  $accountNumber,
        public ?float  $fraction,
        public string  $fullName,
        public ?string $email,
        public ?string $phone,
        public ?string $membershipDate,
        public ?string $membershipDutyInfo,
        public ?string $addPhone,
        public ?string $address,
        public ?string $postAddress,
        public ?string $note,
        public array   $accounts = [],
    )
    {
    }
}
