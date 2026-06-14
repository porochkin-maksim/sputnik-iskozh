<?php declare(strict_types=1);

namespace Core\App\Account\Import;

readonly class SaveImportedAccountsInput
{
    /**
     * @param array[] $accounts  массив данных из JSON-сериализации AccountImportItem (см. AccountImportItem::jsonSerialize)
     */
    public function __construct(
        public array $accounts,
    )
    {
    }

    /**
     * @return AccountImportData[]
     */
    public function getAccounts(): array
    {
        return array_map(
            static fn(array $data) => new AccountImportData(
                id             : $data['id'],
                number         : $data['number'] ?? '',
                size           : $data['size'] ?? null,
                cadastreNumber : $data['cadastreNumber'] ?? null,
            ),
            $this->accounts,
        );
    }
}
