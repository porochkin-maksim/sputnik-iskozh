<?php declare(strict_types=1);

use App\Models\Billing\Period;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\Money\MoneyService;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Repositories\SearcherInterface;

$periods = app(PeriodService::class)->search(
    PeriodSearcher::make()->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC),
)->getItems();

$periodId = (int) request()->get('period', null);
$period   = app(PeriodService::class)->getById($periodId);
$period   = $period ? : $periods->first();

$invoices = new InvoiceCollection();
$payments = new PaymentCollection();

if (lc::account()->getId() && $period) {
    $services = app(ServiceService::class)->search(
        (new ServiceSearcher())->setPeriodId($period->getId()),
    )->getItems();

    $invoices = app(InvoiceService::class)->search(
        (new InvoiceSearcher())
            ->setWithPayments()
            ->setWithClaims()
            ->setPeriodId($period->getId())
            ->setAccountId(lc::account()->getId()),
    )->getItems();

    $payments = app(PaymentService::class)->search(
        (new PaymentSearcher())->setInvoiceIds($invoices->getIds()),
    )->getItems();
}
$account     = lc::account();
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_INVOICES, $period);

?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_INVOICES, $period) }}
    @if($account->getId())
        <h3 class="text-dark">
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center text-wrap">
                <span class="text-center">Счета на участок "{{ $account->getNumber() }}" в периоде</span>
                <form action="{{ route(RouteNames::PROFILE_INVOICES) }}"
                      class="d-inline-block">
                    <select name="period"
                            class="form-select form-select-sm me-2 d-inline-block fw-bold ms-2"
                            style="width: auto;font-size: 1em;"
                            onchange="this.form.submit()">
                        @foreach($periods as $p)
                            <option value="{{ $p->getId() }}" {{ $period && $period->getId() === $p->getId() ? 'selected' : '' }}>
                                {{ $p->getName() }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </h3>
        @if($invoices->count())
            <div class="table-responsive">
                <table class="table table-sm table-bordered  mb-0">
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr class="text-center table-success">
                            <th class="text-start">Взнос</th>
                            <th>Тариф</th>
                            <th>Стоимость</th>
                            <th>Оплачено</th>
                            <th>Долг</th>
                        </tr>
                        @foreach($invoice->getClaims()->sortByServiceTypes() as $claim)
                            <tr>
                                <td>{{ $claim->getName() ?: $services->getById($claim->getServiceId())->getName() }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getTariff()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getCost()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getPaid()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getDelta()) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <th class="text-end"
                                colspan="2">Итого
                            </th>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getCost()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getPaid()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getDelta()) }}</td>
                        </tr>
                        @if($payments->count())
                            <tr class="text-center border">
                                <th colspan="5">Платежи</th>
                            </tr>
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="border-end-0"></td>
                                    <td colspan="2" class="border-start-0">
                                        {{ $payment->getCreatedAt()->translatedFormat('d F Y H:i') }}
                                    </td>
                                    <td class="border-end-0 text-end">
                                        {{ MoneyService::parse($payment->getCost()) }}
                                    </td>
                                    <td class="border-start-0"></td>
                                </tr>
                            @endforeach
                        @endif

                        @if (!$invoice->isPaid())
                            <tr class="text-center">
                                <td colspan="5">
                                    @if (app(AcquiringService::class)->isAvailable())
                                        @php
                                            $acquiringAmounts = [];
                                            if ( ! $invoice->getPaid()) {
                                                if (($account->getFraction() ?: 1) !== 1){
                                                    $acquiringAmounts[] = MoneyService::toFloat(
                                                        MoneyService::parse($invoice->getDelta())->multiply($account->getFraction() ?: 1)
                                                    );
                                                }
                                            }

                                            $acquiringAmounts[] = $invoice->getDelta();
                                        @endphp
                                        <div class="d-flex flex-column flex-sm-row text-center justify-content-center align-items-center flex-wrap">
                                            @foreach($acquiringAmounts as $amount)
                                                <form method="POST"
                                                      action="{{ route(RouteNames::ACQURING_INVOICE_CREATE, [$invoice->getId(), $amount]) }}"
                                                      target="_blank">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success mb-2 mx-1">
                                                        <i class="fa fa-credit-card"></i> Оплатить {{ MoneyService::parse($amount) }}
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    @endif
                                    <a class="btn btn-sm btn-outline-primary mx-1 mb-2"
                                       href="{{ route(RouteNames::REQUESTS_PAYMENT, ['invoice' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]) }}">
                                        <i class="fa fa-envelope"></i> Сообщить о совершённом платеже
                                    </a>
                                </td>
                            </tr>
                        @endif
                        <tr class="text-center">
                            <td colspan="5">
                                <a href="{{ route(RouteNames::DOCUMENT_RECEIPT_INVOICE, ['uid' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]) }}"
                                   target="_blank" class="btn btn-outline-danger">
                                    <i class="fa fa-file-pdf-o text-danger"></i> Получить квитанцию
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <hr>
            <h6 class="text-center text-secondary m-0">
                <i>счетов нет...</i>
            </h6>
        @endif
        <div>
            <hr>
        </div>
    @endif
    @if($periods->count())
        <h3 class="text-dark text-center">Статистика СНТ в периоде «{{ $period->getName() }}»</h3>
        <summary-block :period-id="{{ $period->getId() }}"
                       :show-invoice="false" />
    @endif
@endsection
