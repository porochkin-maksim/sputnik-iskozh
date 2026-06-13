<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Payment\PaymentEntity;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailSheet extends BaseExportSheet
{
    private int $row = 1;

    protected function buildSheet(Worksheet $sheet): void
    {
        $this->buildHeader($sheet);
        $this->buildData($sheet);
        $this->applyStyles($sheet);
    }

    private function buildHeader(Worksheet $sheet): void
    {
        $sheet->fromArray([[
            'Участок', 'Период', 'Тип счёта', 'Услуга / Платёж',
            'Кол-во', 'Тариф', 'Стоимость', 'Оплачено', 'Долг',
            'Дата платежа', 'Примечание',
        ]], null, 'A1');
    }

    private function buildData(Worksheet $sheet): void
    {
        $this->row = 2;

        foreach ($this->invoices as $invoice) {
            $this->buildInvoiceHeader($sheet, $invoice);

            $claims   = $invoice->getClaims();
            $payments = $invoice->getPayments();

            if ($claims) {
                foreach ($claims as $claim) {
                    $this->buildClaimRow($sheet, $claim);
                }
            }

            if ($payments) {
                foreach ($payments as $payment) {
                    $this->buildPaymentRow($sheet, $payment);
                }
            }

            $this->row++;
        }
    }

    private function buildInvoiceHeader(Worksheet $sheet, InvoiceEntity $invoice): void
    {
        $account = $invoice->getAccount();
        $period  = $invoice->getPeriod();
        $type    = $invoice->getType();
        $delta   = $invoice->getCost() !== null && $invoice->getPaid() !== null
            ? $invoice->getCost() - $invoice->getPaid()
            : null;

        $sheet->setCellValue([1, $this->row], $account?->getNumber());
        $sheet->setCellValue([2, $this->row], $period?->getName());
        $sheet->setCellValue([3, $this->row], $type?->name());
        $sheet->setCellValue([4, $this->row], 'Счёт #' . $invoice->getId());
        $sheet->setCellValue([7, $this->row], $invoice->getCost());
        $sheet->setCellValue([8, $this->row], $invoice->getPaid());
        $sheet->setCellValue([9, $this->row], $delta);

        $this->styleInvoiceRow($sheet);
    }

    private function buildClaimRow(Worksheet $sheet, ClaimEntity $claim): void
    {
        $name    = $claim->getName() ?: $claim->getService()?->getName() ?: $claim->getService()?->getType()?->name();
        $delta   = $claim->getCost() !== null && $claim->getPaid() !== null
            ? $claim->getCost() - $claim->getPaid()
            : null;

        $sheet->setCellValue([4, $this->row], '  ' . $name);
        $sheet->setCellValue([5, $this->row], $claim->getQuantity());
        $sheet->setCellValue([6, $this->row], $claim->getTariff());
        $sheet->setCellValue([7, $this->row], $claim->getCost());
        $sheet->setCellValue([8, $this->row], $claim->getPaid());
        $sheet->setCellValue([9, $this->row], $delta);

        $this->row++;
    }

    private function buildPaymentRow(Worksheet $sheet, PaymentEntity $payment): void
    {
        $name   = $payment->getName();
        $paidAt = $payment->getPaidAt();

        $sheet->setCellValue([4, $this->row], '  Платёж: ' . ($name ?: '#'.$payment->getId()));
        $sheet->setCellValue([8, $this->row], $payment->getCost());
        $sheet->setCellValue([10, $this->row], $paidAt?->format('d.m.Y H:i'));
        $sheet->setCellValue([11, $this->row], $payment->getComment());

        $this->stylePaymentRow($sheet);
        $this->row++;
    }

    private function styleInvoiceRow(Worksheet $sheet): void
    {
        $style = $sheet->getStyle('A' . $this->row . ':K' . $this->row);
        $style->getFont()->setBold(true)->setSize(11);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E2F3');
        $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->row++;
    }

    private function stylePaymentRow(Worksheet $sheet): void
    {
        $style = $sheet->getStyle('A' . $this->row . ':K' . $this->row);
        $style->getFont()->setSize(10);
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E9');
        $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function applyStyles(Worksheet $sheet): void
    {
        $lastColumn = 11;

        $headerRange = 'A1:' . $this->colLetter($lastColumn) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $this->setColumnWidths($sheet, [
            1 => 15, 2 => 20, 3 => 15, 4 => 32, 5 => 8, 6 => 12,
            7 => 12, 8 => 12, 9 => 12, 10 => 18, 11 => 30,
        ]);

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Детализация';
    }
}
