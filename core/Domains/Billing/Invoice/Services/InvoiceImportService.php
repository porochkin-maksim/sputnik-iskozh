<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Services;

use App\Imports\Payments\PaymentsImport;
use App\Imports\Payments\Sheet;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceImportDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Jobs\SaveImportPaymentsJob;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Jobs\LockedJob;
use Core\Domains\Infra\DbLock\LockLocator;
use Core\Domains\Infra\DbLock\Service\LockService;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InvoiceImportService
{
    private InvoiceService $invoiceService;
    private LockService    $lockService;

    public function __construct()
    {
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->lockService    = LockLocator::LockService();
    }

    /**
     * Парсит Excel-файл и возвращает структурированные данные для фронта
     */
    public function parseFile(
        UploadedFile  $fileMain,
        ?UploadedFile $filePrev,
        PeriodDTO     $period,
        string        $colAccrued,
        string        $colPaid,
        string        $colDebt,
    ): array
    {
        $spreadsheet = IOFactory::load($fileMain->getRealPath());

        // Импорт данных из Excel
        $import = new PaymentsImport(
            $colAccrued,
            $colPaid,
            $colDebt,
            $spreadsheet->getSheetCount(),
        );

        $importOld = new PaymentsImport(
            $colAccrued,
            $colPaid,
            $colDebt,
            $spreadsheet->getSheetCount(),
        );

        Excel::import($import, $fileMain);

        if ($filePrev) {
            Excel::import($importOld, $filePrev);
        }

        $sheetsData     = $import->getSheetsData();
        $sheetsDataPrev = $importOld->getSheetsData();

        $invoices = $this->invoiceService->search(
            new InvoiceSearcher()
                ->setPeriodId($period->getId())
                ->setWithAccount(),
        )
            ->getItems()
            ->setCheckClass(false)
            ->mapWithKeys(function (InvoiceDTO $invoiceDTO) {
                /** @var AccountDTO $account */
                $account = $invoiceDTO->getAccount();

                $invoiceCost = MoneyService::parse($invoiceDTO->getCost());
                $advanceCost = MoneyService::parse($invoiceDTO->getAdvance());
                $debtCost    = MoneyService::parse($invoiceDTO->getDebt());

                return [
                    $account->getNumber() => [
                        InvoiceImportDTO::ACCOUNT_NUMBER  => $account->getNumber(),
                        InvoiceImportDTO::ACCOUNT_ID      => $account->getId(),
                        InvoiceImportDTO::INVOICE_ID      => $invoiceDTO->getId(),
                        InvoiceImportDTO::INVOICE_MAIN    => MoneyService::toFloat($invoiceCost->subtract($advanceCost)->subtract($debtCost)),
                        InvoiceImportDTO::INVOICE_COST    => $invoiceDTO->getCost(),
                        InvoiceImportDTO::INVOICE_PAID    => $invoiceDTO->getPaid(),
                        InvoiceImportDTO::INVOICE_DELTA   => $invoiceDTO->getDelta(),
                        InvoiceImportDTO::INVOICE_ADVANCE => $invoiceDTO->getAdvance(),
                        InvoiceImportDTO::INVOICE_DEBT    => $invoiceDTO->getDebt(),
                    ],
                ];
            })
            ->toArray()
        ;

        $result = [];

        foreach ($sheetsData as $sheetIndex => $rows) {
            $district    = $sheetIndex + 1;
            $sheetResult = [
                'district' => $district,
                'items'    => [],
            ];
            foreach ($rows as $rowIndex => $row) {
                $accountNumber = $row[Sheet::ACCOUNT_NUMBER];

                /** @var null|array<string, mixed> $invoice */
                $invoice = $invoices[$accountNumber] ?? null;

                $dto = InvoiceImportDTO::fromArray([
                    InvoiceImportDTO::ACCOUNT_NUMBER  => $accountNumber,
                    InvoiceImportDTO::INVOICE_ID      => $invoice[InvoiceImportDTO::INVOICE_ID] ?? null,
                    InvoiceImportDTO::ACCOUNT_ID      => $invoice[InvoiceImportDTO::ACCOUNT_ID] ?? null,
                    InvoiceImportDTO::INVOICE_MAIN    => $invoice[InvoiceImportDTO::INVOICE_MAIN] ?? null,
                    InvoiceImportDTO::INVOICE_COST    => $invoice[InvoiceImportDTO::INVOICE_COST] ?? null,
                    InvoiceImportDTO::INVOICE_PAID    => $invoice[InvoiceImportDTO::INVOICE_PAID] ?? null,
                    InvoiceImportDTO::INVOICE_DELTA   => $invoice[InvoiceImportDTO::INVOICE_DELTA] ?? null,
                    InvoiceImportDTO::INVOICE_ADVANCE => $invoice[InvoiceImportDTO::INVOICE_ADVANCE] ?? null,
                    InvoiceImportDTO::INVOICE_DEBT    => $invoice[InvoiceImportDTO::INVOICE_DEBT] ?? null,
                    InvoiceImportDTO::COST            => $row[Sheet::COST] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::COST] ?? 0),
                    InvoiceImportDTO::PAID            => $row[Sheet::PAID] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::PAID] ?? 0),
                    InvoiceImportDTO::DEBT            => $row[Sheet::DEBT] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::DEBT] ?? 0),
                ]);

                $sheetResult['items'][] = $dto;
            }

            $result[] = $sheetResult;
        }

        return $result;
    }

    /**
     * Сохраняет платежи на основе переданных данных
     */
    public function savePayments(array $paymentsData): void
    {
        $lockName = LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB;

        if ( ! $this->lockService->isAvailable($lockName)) {
            abort(403, 'Задача уже запущена');
        }

        $this->lockService->lock($lockName);

        // Оборачиваем в LockedJob
        dispatch(new LockedJob(
            SaveImportPaymentsJob::class,
            [$paymentsData],
            $lockName,
        )->onQueue(QueueEnum::DEFAULT->value));
    }
}