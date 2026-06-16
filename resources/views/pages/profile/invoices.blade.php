<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;

/**
 * @var PeriodCollection             $periods
 * @var null|PeriodEntity            $period
 * @var InvoiceCollection            $invoices
 * @var PaymentCollection            $payments
 * @var null|TicketServiceCollection $services
 * @var AccountEntity                $account
 * @var bool                         $acquiringAvailable
 */
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_INVOICES, $period);
$formatMoney = static fn(mixed $amount): string => number_format((float) $amount, 2, ',', ' ') . ' ₽';

$periodOptions = [];
foreach ($periods as $p) {
    $periodOptions[] = [
        'id'       => $p->getId(),
        'name'     => $p->getName(),
        'selected' => $period && $period->getId() === $p->getId(),
    ];
}

$invoiceItems = [];
foreach ($invoices as $invoice) {
    $claims = [];
    foreach ($invoice->getClaims()->sortByServiceTypes() as $claim) {
        $claims[] = [
            'id'           => $claim->getId(),
            'name'         => $claim->getName() ? : $claim->getService()?->getName() ? : $services->getById($claim->getServiceId())->getName(),
            'tariff'       => $formatMoney($claim->getTariff()),
            'cost'         => $formatMoney($claim->getCost()),
            'paid'         => $formatMoney($claim->getPaid()),
            'delta'        => $formatMoney($claim->getDelta()),
            'deltaNumeric' => (float) $claim->getDelta(),
            'isPaid'       => $claim->isPaid(),
        ];
    }

    $paymentsItems = [];
    foreach ($invoice->getPayments() ? : [] as $payment) {
        $paymentsItems[] = [
            'id'   => $payment->getId(),
            'date' => $payment->getCreatedAt()->translatedFormat('d F Y H:i'),
            'cost' => $formatMoney($payment->getCost()),
        ];
    }

    $acquiringAmounts = [];
    if ($acquiringAvailable && ! $invoice->getPaid()) {
        if (($account->getFraction() ? : 1) !== 1) {
            $amount = MoneyService::toFloat(
                MoneyService::parse($invoice->getDelta())->multiply($account->getFraction() ? : 1),
            );

            $acquiringAmounts[] = [
                'label' => $formatMoney($amount),
                'url'   => route(RouteNames::ACQURING_INVOICE_CREATE, [$invoice->getId(), $amount]),
            ];
        }
    }

    $acquiringAmounts[] = [
        'label' => $formatMoney($invoice->getDelta()),
        'url'   => route(RouteNames::ACQURING_INVOICE_CREATE, [$invoice->getId(), $invoice->getDelta()]),
    ];

    $invoiceItems[] = [
        'id'           => $invoice->getId(),
        'title'        => $invoice->getName(),
        'cost'         => $formatMoney($invoice->getCost()),
        'paid'         => $formatMoney($invoice->getPaid()),
        'delta'        => $formatMoney($invoice->getDelta()),
        'deltaNumeric' => (float) $invoice->getDelta(),
        'isPaid'       => $invoice->isPaid(),
        'claims'       => $claims,
        'payments'     => $paymentsItems,
        'acquiring'    => $acquiringAmounts,
        'paymentUrl'   => route(RouteNames::REQUESTS_PAYMENT, ['invoice' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]),
        'receiptUrl'   => route(RouteNames::DOCUMENT_RECEIPT_INVOICE, ['uid' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]),
    ];
}

?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_INVOICES, $period) }}
    @if($account->getId())
        <div class="page-hero">
            <h3 class="page-hero__title text-dark">
                Счета на участок "{{ $account->getNumber() }}"
            </h3>
            <div class="page-hero__lead">
                Просмотр начислений, оплат и квитанций по выбранному периоду.
            </div>
        </div>
        <profile-invoices-block
                :periods='@json($periodOptions)'
                :selected-period-id='{{ $period ? $period->getId() : 'null' }}'
                period-route='{{ route(RouteNames::PROFILE_INVOICES) }}'
                :invoices='@json($invoiceItems)'
                :acquiring-available='{{ $acquiringAvailable ? 'true' : 'false' }}'
                csrf-token='{{ csrf_token() }}'
        ></profile-invoices-block>
        <div>
            <hr>
        </div>
    @endif
    @if($periods->count() && $period)
        <h3 class="text-dark text-center">Статистика СНТ в периоде «{{ $period->getName() }}»</h3>
        <summary-block :period-id="{{ $period->getId() }}"
                       :show-invoice="false"></summary-block>
    @endif
@endsection
