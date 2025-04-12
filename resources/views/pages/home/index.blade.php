<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
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

$period = PeriodLocator::PeriodService()->getCurrentPeriod();
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
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    <div>
        <h3 class="text-dark">{{ lc::userDecorator()->getFullName() }}</h3>
        @if(lc::account()->getId())
            <h5 class="text-dark"> Участок: {{ lc::account()->getNumber() }}</h5>
            <h5 class="text-dark"> Площадь: {{ lc::account()->getSize() }}м²</h5>
        @endif
    </div>
    <div>
        <password-block :account='@json(new AccountResource(lc::account()))'
                        :user='@json(new UserResource(lc::user()))'></password-block>
    </div>
    @if(lc::account()->getId())
        <div class="mt-2">
            <hr>
        </div>
        <profile-counters-block></profile-counters-block>
    @endif
    @if($period)
        <div>
            <hr>
        </div>
        <h3 class="text-dark text-center">Статистика СНТ в периоде <b class="text-nowrap">«{{ $period->getName() }}»</b>
        </h3>
        <summary-block :period-id="{{ $period->getId() }}"
                       :show-invoice="false"></summary-block>
    @endif
    @if(lc::account()->getId())
        @if($invoices->count())
            <div>
                <hr>
            </div>
            <table class="table table-borderless">
                <thead class="text-center">
                <tr>
                    <th colspan="4">
                        <h3 class="text-dark">Ваши платежи в этом периоде</h3>
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
                    </tr>
                    @foreach($invoice->getClaims() as $claim)
                        <tr>
                            <td>{{ $claim->getName() ?: $services->getById($claim->getServiceId())->getName() }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getTariff()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getCost()) }}</td>
                            <td class="text-end text-nowrap">{{ MoneyService::parse($claim->getPayed()) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <th class="text-end"
                            colspan="2">Итого
                        </th>
                        <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getCost()) }}</td>
                        <td class="text-end text-nowrap">{{ MoneyService::parse($invoice->getPayed()) }}</td>
                    </tr>
                    <tr>
                        <th colspan="4"></th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endif
@endsection