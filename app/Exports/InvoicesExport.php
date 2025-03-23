<?php

namespace App\Exports;

use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Enums\DateTimeFormat;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InvoicesExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    public function __construct(
        private InvoiceCollection $invoices,
    )
    {
    }

    public function array(): array
    {
        $result = [];

        foreach ($this->invoices as $invoice) {
            $result[] = [
                $invoice->getId(),
                $invoice->getPeriod()?->getName(),
                $invoice->getAccount()?->getNumber(),
                $invoice->getType()?->name(),
                '',
                $invoice->getCost(),
                $invoice->getPayed(),
                $invoice->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
                $invoice->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            ];

            foreach ($invoice->getTransactions() as $transaction) {
                $result[] = [
                    '',
                    '',
                    '',
                    $transaction->getService()?->getName(),
                    $transaction->getName() === $transaction->getService()?->getName() ? '' : $transaction->getName(),
                    $transaction->getCost(),
                    $transaction->getPayed(),
                    $transaction->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
                    $transaction->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
                ];
            }
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            '№',
            'Период',
            'Участок',
            'Тип',
            'Транзакция',
            'Стоимость',
            'Оплачено',
            'Создан',
            'Обновлён',
        ];
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function(AfterSheet $event) {
            //     $cellRange = "A1:W1";
            //     $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            // },
        ];
    }
}
