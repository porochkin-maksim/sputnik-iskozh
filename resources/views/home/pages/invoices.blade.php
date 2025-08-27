<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Models\Billing\Period;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Models\InvoiceSearcher;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Money\MoneyService;

$periods = PeriodLocator::PeriodService()->search(
    PeriodSearcher::make()->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC),
)->getItems();

$periodId = (int) request()->get('period', null);
$period   = PeriodLocator::PeriodService()->getById($periodId);

$period = $period ? : PeriodLocator::PeriodService()->getCurrentPeriod();
if ( ! $period) {
    $period = PeriodLocator::PeriodService()->search(PeriodSearcher::make())->getItems()->first();
}
$invoices = new InvoiceCollection();

if (lc::account()->getId() && $period) {
    $serviceSearcher = new ServiceSearcher();
    $serviceSearcher->setPeriodId($period->getId());
    $services = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems();

    $invoiceSearcher = new InvoiceSearcher();
    $invoiceSearcher->setWithPayments()
        ->setWithClaims()
        ->setPeriodId($period->getId())
        ->setAccountId(lc::account()->getId())
    ;

    $invoices = InvoiceLocator::InvoiceService()->search($invoiceSearcher)->getItems();
}

$account     = lc::account();
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_INVOICES, $period);
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_INVOICES, $period) }}
    @if($account->getId())
        @if($invoices->count())
            <h3 class="text-dark">
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center text-wrap">
                    <span>Счета на участок "{{ $account->getNumber() }}" в периоде</span>
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
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                    @foreach($invoices as $invoice)
                        <tr class="text-end table-success border-success">
                            <th class="text-start">Взнос</th>
                            <th>Тариф</th>
                            <th>Стоимость</th>
                            <th>Оплачено</th>
                            <th>Долг</th>
                        </tr>
                        @foreach($invoice->getClaims() as $claim)
                            <tr>
                                <td>{{ $claim->getName() ?: $services->getById($claim->getServiceId())->getName() }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getTariff()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getCost()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getPayed()) }}</td>
                                <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getDelta()) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <th class="text-end"
                                colspan="2">Итого
                            </th>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getCost()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getPayed()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getDelta()) }}</td>
                        </tr>
                        @if ($invoice->isPayed())
                            <tr class="text-center table-success border-success text-success">
                                <th colspan="5">Оплачено</th>
                            </tr>
                        @else
                            <tr class="text-center">
                                <td colspan="5">
                                    <div class="d-flex flex-column flex-sm-row text-center justify-content-center align-items-center flex-wrap">
                                        @if ($account->getFraction() === 1 || ! $account->getFraction())
                                            <button class="btn btn-sm btn-success mb-2">
                                                <i class="fa fa-credit-card"></i> Оплатить {{ MoneyService::parse($invoice->getDelta()) }}
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-success mx-1 mb-2">
                                                <i class="fa fa-credit-card"></i> Оплатить {{ $account->getFractionPercent() }}: {{ MoneyService::parse($invoice->getDelta() * $account->getFraction()) }}
                                            </button>
                                            <button class="btn btn-sm btn-outline-success mx-1 mb-2">
                                                <i class="fa fa-credit-card"></i> Оплатить 100%: {{ MoneyService::parse($invoice->getDelta()) }}
                                            </button>
                                        @endif
                                    </div>
                                    <a class="btn btn-sm btn-outline-primary mx-1 mb-2"
                                       href="{{ route(RouteNames::PAYMENT, ['invoice' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]) }}">
                                        <i class="fa fa-envelope"></i> Сообщить о совершённом платеже
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <hr>
            </div>
        @endif
    @endif
    @if($periods->count())
        <h3 class="text-dark text-center">Статистика СНТ в периоде «{{ $period->getName() }}»</h3>
        <summary-block :period-id="{{ $period->getId() }}"
                       :show-invoice="false" />
    @endif
@endsection