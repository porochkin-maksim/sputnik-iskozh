<?php declare(strict_types=1);

use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Enums\DateTimeFormat;

/**
 * @var InvoiceCollection $invoices
 */
?>
<style>
    table, th, td { border: 1px solid black; }
</style>
<table>
    <thead>
    <tr>
        <th class="text-center">№</th>
        <th class="text-center">Тип</th>
        <th class="text-center">Период</th>
        <th class="text-center">Участок</th>
        <th class="text-center">Стоимость</th>
        <th class="text-center">Оплачено</th>
        <th class="text-center">Создан</th>
        <th class="text-center">Обновлён</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->getId() }}</td>
            <td>{{ $invoice->getType()?->name() }}</td>
            <td>{{ $invoice->getPeriod()?->getName() }}</td>
            <td>{{ $invoice->getAccount()?->getNumber() }}</td>
            <td>{{ $invoice->getCost() }}</td>
            <td>{{ $invoice->getPayed() }}</td>
            <td>{{ $invoice->getCreatedAt()->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT) }}</td>
            <td>{{ $invoice->getUpdatedAt()->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
