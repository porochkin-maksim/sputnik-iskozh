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

$account = lc::account();
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    @if($account->getId())
        @if($invoices->count())
            <table class="table table-borderless">
                <thead class="text-center">
                <tr>
                    <th colspan="5">
                        <h3 class="text-dark">Счета на участок "{{ $account->getNumber() }}" в периоде "{{ $period->getName() }}"</h3>
                    </th>
                </tr>
                </thead>
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
                            <td colspan="5"
                                class="pe-0">
                                @if ($account->getFraction() === 1 || ! $account->getFraction())
                                    <button class="btn btn-sm btn-success">Оплатить {{ MoneyService::parse($invoice->getDelta()) }}</button>
                                @else
                                    <button class="btn btn-sm btn-outline-success me-1">
                                        Оплатить {{ $account->getFractionPercent() }}: {{ MoneyService::parse($invoice->getDelta() * $account->getFraction()) }}
                                    </button>
                                    или
                                    <button class="btn btn-sm btn-outline-success ms-1">
                                        Оплатить 100%: {{ MoneyService::parse($invoice->getDelta()) }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th colspan="4"></th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endif
    @if($periods->count())
        <div>
            <hr>
        </div>
        <div class="d-flex flex-md-row flex-column-reverse align-items-center justify-content-md-between justify-content-center mb-3">
            <h3 class="text-dark mb-0 text-center mt-2 mt-md-0">Статистика СНТ в периоде «{{ $period->getName() }}»</h3>
            <form action="{{ route(RouteNames::PROFILE_INVOICES) }}"
                  method="GET"
                  class="d-flex align-items-center">
                <select name="period"
                        class="form-select form-select-sm me-2"
                        style="width: auto;"
                        onchange="this.form.submit()">
                    @foreach($periods as $p)
                        <option value="{{ $p->getId() }}" {{ $period && $period->getId() === $p->getId() ? 'selected' : '' }}>
                            {{ $p->getName() }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <summary-block :period-id="{{ $period->getId() }}"
                       :show-invoice="false"></summary-block>
    @endif
@endsection