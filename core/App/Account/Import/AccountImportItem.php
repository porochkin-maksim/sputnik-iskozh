<?php declare(strict_types=1);

namespace Core\App\Account\Import;

use App\Resources\RouteNames;
use JsonSerializable;

readonly class AccountImportItem implements JsonSerializable
{
    private const array FIELDS = [
        'number', 'size', 'cadastreNumber',
    ];

    public function __construct(
        private AccountImportData  $importData,
        private ?AccountImportData $dbData,
    )
    {
    }

    public function hasChanges(): bool
    {
        if ($this->dbData === null) {
            return true;
        }

        return array_any(self::FIELDS, fn($field) => (string) $this->importData->$field !== (string) $this->dbData->$field);
    }

    public function jsonSerialize(): array
    {
        $changed = [];
        foreach (self::FIELDS as $field) {
            $changed[$field] = $this->dbData !== null &&
                               (string) $this->importData->$field !== (string) $this->dbData->$field;
        }

        return [
            'id'               => $this->importData->id,
            'isNew'            => $this->importData->id === null,
            'number'           => $this->importData->number,
            'size'             => $this->importData->size,
            'cadastreNumber'   => $this->importData->cadastreNumber,
            'dbId'             => $this->dbData?->id,
            'dbNumber'         => $this->dbData?->number,
            'dbSize'           => $this->dbData?->size,
            'dbCadastreNumber' => $this->dbData?->cadastreNumber,
            'changed'          => $changed,
            'accountUrl'       => $this->importData->id ? route(RouteNames::ADMIN_ACCOUNT_VIEW, [$this->importData->id]) : null,
        ];
    }
}
