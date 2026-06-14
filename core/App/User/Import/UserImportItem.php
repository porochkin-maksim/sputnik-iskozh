<?php declare(strict_types=1);

namespace Core\App\User\Import;

use App\Resources\RouteNames;
use JsonSerializable;

readonly class UserImportItem implements JsonSerializable
{
    private const array FIELDS = [
        'accountNumber', 'fraction', 'fullName', 'email', 'phone',
        'membershipDate', 'membershipDutyInfo', 'addPhone',
        'address', 'postAddress', 'note',
    ];

    public function __construct(
        private UserImportData  $importData,
        private ?UserImportData $dbData,
        private ?string         $accountError,
    )
    {
    }

    public function hasChanges(): bool
    {
        if ($this->dbData === null) {
            return true;
        }

        return array_any(self::FIELDS, fn($field) => $this->importData->$field !== $this->dbData->$field);
    }

    public function jsonSerialize(): array
    {
        $changed = [];
        foreach (self::FIELDS as $field) {
            $changed[$field] = $this->dbData !== null &&
                               $this->importData->$field !== $this->dbData->$field;
        }

        return [
            'id'                   => $this->importData->id,
            'isNew'                => $this->importData->id === null,
            'accountNumber'        => $this->importData->accountNumber,
            'fraction'             => $this->importData->fraction,
            'fullName'             => $this->importData->fullName,
            'email'                => $this->importData->email,
            'phone'                => $this->importData->phone,
            'membershipDate'       => $this->importData->membershipDate,
            'membershipDutyInfo'   => $this->importData->membershipDutyInfo,
            'addPhone'             => $this->importData->addPhone,
            'address'              => $this->importData->address,
            'postAddress'          => $this->importData->postAddress,
            'note'                 => $this->importData->note,
            'dbId'                 => $this->dbData?->id,
            'dbAccountNumber'      => $this->dbData?->accountNumber,
            'dbFraction'           => $this->dbData?->fraction,
            'dbFullName'           => $this->dbData?->fullName,
            'dbEmail'              => $this->dbData?->email,
            'dbPhone'              => $this->dbData?->phone,
            'dbMembershipDate'     => $this->dbData?->membershipDate,
            'dbMembershipDutyInfo' => $this->dbData?->membershipDutyInfo,
            'dbAddPhone'           => $this->dbData?->addPhone,
            'dbAddress'            => $this->dbData?->address,
            'dbPostAddress'        => $this->dbData?->postAddress,
            'dbNote'               => $this->dbData?->note,
            'changed'              => $changed,
            'accountError'         => $this->accountError,
            'userUrl'              => $this->importData->id ? route(RouteNames::ADMIN_USER_VIEW, [$this->importData->id]) : null,
        ];
    }
}
