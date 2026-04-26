<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Imports\Payments\PaymentsImport;
use App\Imports\Payments\Sheet;
use App\Services\Money\MoneyService;
use App\Services\Queue\QueueEnum;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Jobs\SaveImportPaymentsJob;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Jobs\LockedJob;
use Core\Domains\Infra\DbLock\Service\LockService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

readonly class InvoiceImportService
{
    public function __construct(
        private InvoiceService $invoiceService,
        private LockService    $lockService,
    )
    {
    }

    public function parseFile(
        UploadedFile  $fileMain,
        ?UploadedFile $filePrev,
        PeriodEntity  $period,
        string        $colAccrued,
        string        $colPaid,
        string        $colDebt,
    ): array
    {
        $spreadsheet = IOFactory::load($fileMain->getPath());

        $import    = new PaymentsImport($colAccrued, $colPaid, $colDebt, $spreadsheet->getSheetCount());
        $importOld = new PaymentsImport($colAccrued, $colPaid, $colDebt, $spreadsheet->getSheetCount());

        Excel::import($import, $fileMain->getPath());

        if ($filePrev) {
            Excel::import($importOld, $filePrev->getPath());
        }

        $sheetsData     = $import->getSheetsData();
        $sheetsDataPrev = $importOld->getSheetsData();

        $invoices = $this->invoiceService->search(
            (new InvoiceSearcher())
                ->setPeriodId($period->getId())
                ->setWithAccount(),
        )->getItems()->map(function (InvoiceEntity $invoice) {
            /** @var AccountEntity $account */
            $account = $invoice->getAccount();

            $invoiceCost = MoneyService::parse($invoice->getCost());
            $advanceCost = MoneyService::parse($invoice->getAdvance());
            $debtCost    = MoneyService::parse($invoice->getDebt());

            return [
                $account->getNumber() => [
                    InvoiceImportItem::ACCOUNT_NUMBER  => $account->getNumber(),
                    InvoiceImportItem::ACCOUNT_ID      => $account->getId(),
                    InvoiceImportItem::INVOICE_ID      => $invoice->getId(),
                    InvoiceImportItem::INVOICE_MAIN    => MoneyService::toFloat($invoiceCost->subtract($advanceCost)->subtract($debtCost)),
                    InvoiceImportItem::INVOICE_COST    => $invoice->getCost(),
                    InvoiceImportItem::INVOICE_PAID    => $invoice->getPaid(),
                    InvoiceImportItem::INVOICE_DELTA   => $invoice->getDelta(),
                    InvoiceImportItem::INVOICE_ADVANCE => $invoice->getAdvance(),
                    InvoiceImportItem::INVOICE_DEBT    => $invoice->getDebt(),
                ],
            ];
        })->toArray();

        $result = [];

        foreach ($sheetsData as $sheetIndex => $rows) {
            $district    = $sheetIndex + 1;
            $sheetResult = [
                'district' => $district,
                'items'    => [],
            ];

            foreach ($rows as $rowIndex => $row) {
                $accountNumber = $row[Sheet::ACCOUNT_NUMBER];
                $invoice       = $invoices[$accountNumber] ?? null;

                $dto = InvoiceImportItem::fromArray([
                    InvoiceImportItem::ACCOUNT_NUMBER  => $accountNumber,
                    InvoiceImportItem::INVOICE_ID      => $invoice[InvoiceImportItem::INVOICE_ID] ?? null,
                    InvoiceImportItem::ACCOUNT_ID      => $invoice[InvoiceImportItem::ACCOUNT_ID] ?? null,
                    InvoiceImportItem::INVOICE_MAIN    => $invoice[InvoiceImportItem::INVOICE_MAIN] ?? null,
                    InvoiceImportItem::INVOICE_COST    => $invoice[InvoiceImportItem::INVOICE_COST] ?? null,
                    InvoiceImportItem::INVOICE_PAID    => $invoice[InvoiceImportItem::INVOICE_PAID] ?? null,
                    InvoiceImportItem::INVOICE_DELTA   => $invoice[InvoiceImportItem::INVOICE_DELTA] ?? null,
                    InvoiceImportItem::INVOICE_ADVANCE => $invoice[InvoiceImportItem::INVOICE_ADVANCE] ?? null,
                    InvoiceImportItem::INVOICE_DEBT    => $invoice[InvoiceImportItem::INVOICE_DEBT] ?? null,
                    InvoiceImportItem::COST            => $row[Sheet::COST] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::COST] ?? 0),
                    InvoiceImportItem::PAID            => $row[Sheet::PAID] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::PAID] ?? 0),
                    InvoiceImportItem::DEBT            => $row[Sheet::DEBT] - ($sheetsDataPrev[$sheetIndex][$rowIndex][Sheet::DEBT] ?? 0),
                ]);

                $sheetResult['items'][] = $dto;
            }

            $result[] = $sheetResult;
        }

        return $result;
    }

    public function savePayments(array $paymentsData): void
    {
        $lockName = LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB;

        if ( ! $this->lockService->isAvailable($lockName)) {
            abort(403, 'Задача уже запущена');
        }

        $this->lockService->lock($lockName);

        dispatch(new LockedJob(
            SaveImportPaymentsJob::class,
            [$paymentsData],
            $lockName,
        )->onQueue(QueueEnum::DEFAULT->value));
    }
}
