<?php declare(strict_types=1);

namespace Core\App\Account\Import;

use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use PhpOffice\PhpSpreadsheet\IOFactory;

readonly class AccountsImportService
{
    public function __construct(
        private AccountService $accountService,
    )
    {
    }

    private const int COL_ID       = 0;
    private const int COL_NUMBER   = 1;
    private const int COL_SIZE     = 2;
    private const int COL_CADASTRE = 3;

    /**
     * @return AccountImportItem[]
     */
    public function parseFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = $worksheet->toArray();

        $accountsData = $this->extractRows($rows);

        return $this->buildItems($accountsData);
    }

    /**
     * @return AccountImportData[]
     */
    private function extractRows(array $rows): array
    {
        $result = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue;
            }

            $id        = $this->parseInt($row[self::COL_ID] ?? null);
            $numberStr = trim((string) ($row[self::COL_NUMBER] ?? ''));

            if ($numberStr === '' && $id === null) {
                continue;
            }

            $result[] = new AccountImportData(
                id             : $id,
                number         : $numberStr,
                size           : $this->parseInt($row[self::COL_SIZE] ?? null),
                cadastreNumber : trim((string) ($row[self::COL_CADASTRE] ?? '')) ?: null,
            );
        }

        return $result;
    }

    /**
     * @param AccountImportData[] $accountsData
     * @return AccountImportItem[]
     */
    private function buildItems(array $accountsData): array
    {
        $items = [];

        foreach ($accountsData as $data) {
            $dbAccount = null;

            if ($data->id !== null) {
                $dbAccount = $this->accountService->getById($data->id);
                if ( ! $dbAccount) {
                    continue;
                }
            }
            else {
                if ($data->number !== '') {
                    $dbAccount = $this->accountService->findByNumber($data->number);
                }
            }

            $dbData = $dbAccount ? $this->accountToDbData($dbAccount) : null;

            $items[] = new AccountImportItem(
                importData: $data,
                dbData    : $dbData,
            );
        }

        return $items;
    }

    private function accountToDbData(AccountEntity $account): AccountImportData
    {
        $exData = $account->getExData();
        $size   = $account->getSize();

        return new AccountImportData(
            id             : $account->getId(),
            number         : $account->getNumber() ?? '',
            size           : $size > 0 ? $size : null,
            cadastreNumber : $exData->getCadastreNumber(),
        );
    }

    private function parseInt(mixed $value): ?int
    {
        if (in_array($value, [null, '', 0, '0'], true)) {
            return null;
        }

        return (int) $value;
    }
}
