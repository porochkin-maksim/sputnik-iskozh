<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Services;

use App\Imports\Payments\PaymentsImport;
use App\Imports\Payments\Sheet;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceImportDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InvoiceImportService
{
    private InvoiceService $invoiceService;
    private PaymentService $paymentService;
    private PaymentFactory $paymentFactory;

    public function __construct()
    {
        $this->invoiceService = InvoiceLocator::InvoiceService();
        $this->paymentService = PaymentLocator::PaymentService();
        $this->paymentFactory = PaymentLocator::PaymentFactory();
    }

    /**
     * Парсит Excel-файл и возвращает структурированные данные для фронта
     */
    public function parseFile(
        UploadedFile $file,
        PeriodDTO    $period,
        string       $colAccrued,
        string       $colPaid,
        string       $colDebt,
    ): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());

        // Импорт данных из Excel
        $import = new PaymentsImport(
            $colAccrued,
            $colPaid,
            $colDebt,
            $spreadsheet->getSheetCount(),
        );
        Excel::import($import, $file);

        $sheetsData = $import->getSheetsData(); // массив строк из импорта

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

                return [
                    $account->getNumber() => [
                        InvoiceImportDTO::ACCOUNT_NUMBER => $account->getNumber(),
                        InvoiceImportDTO::ACCOUNT_ID     => $account->getId(),
                        InvoiceImportDTO::INVOICE_ID     => $invoiceDTO->getId(),
                        InvoiceImportDTO::INVOICE_COST   => $invoiceDTO->getCost(),
                        InvoiceImportDTO::INVOICE_PAID   => $invoiceDTO->getPayed(),
                        InvoiceImportDTO::INVOICE_DEBT   => $invoiceDTO->getDelta(),
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
            foreach ($rows as $row) {
                $accountNumber = $row[Sheet::ACCOUNT_NUMBER];

                /** @var null|array<string, mixed> $invoice */
                $invoice = $invoices[$accountNumber] ?? null;

                $dto = InvoiceImportDTO::fromArray([
                    InvoiceImportDTO::ACCOUNT_NUMBER => $accountNumber,
                    InvoiceImportDTO::INVOICE_ID     => $invoice[InvoiceImportDTO::INVOICE_ID] ?? null,
                    InvoiceImportDTO::ACCOUNT_ID     => $invoice[InvoiceImportDTO::ACCOUNT_ID] ?? null,
                    InvoiceImportDTO::INVOICE_COST   => $invoice[InvoiceImportDTO::INVOICE_COST] ?? null,
                    InvoiceImportDTO::INVOICE_PAID   => $invoice[InvoiceImportDTO::INVOICE_PAID] ?? null,
                    InvoiceImportDTO::INVOICE_DEBT   => $invoice[InvoiceImportDTO::INVOICE_DEBT] ?? null,
                    InvoiceImportDTO::COST           => $row[Sheet::COST],
                    InvoiceImportDTO::PAID           => $row[Sheet::PAID],
                    InvoiceImportDTO::DEBT           => $row[Sheet::DEBT],
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
        foreach ($paymentsData as $paymentData) {
            $invoiceId = (int) ($paymentData['invoice_id'] ?? 0);
            $cost      = (float) ($paymentData['amount'] ?? 0);

            if ( ! $invoiceId || ! $cost || $cost <= 0) {
                continue;
            }

            $payment = $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setCost($cost)
                ->setVerified(true)
                ->setModerated(true)
                ->setName('Импортированный платёж')
            ;

            $this->paymentService->save($payment);
        }
    }
}