<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Invoices\InvoiceResource;
use App\Http\Resources\Profile\Users\UserResource;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS_PAYMENT));

$invoiceUid = request()->get('invoice') ? UidFacade::findReferenceId(request()->get('invoice'), UidTypeEnum::INVOICE) : null;
$invoice    = null;
if ($invoiceUid) {
    $invoice = InvoiceLocator::InvoiceService()->getById($invoiceUid);
    if ($invoice) {
        $period = PeriodLocator::PeriodService()->getById($invoice->getPeriodId());
        $invoice->setPeriod($period);
        $account = $invoice->getAccountId() === lc::account()->getId()
            ? lc::account()
            : AccountLocator::AccountService()->getById($invoice->getAccountId());
        $invoice->setAccount($account);
    }
}
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::REQUESTS_PAYMENT) }}
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <div class="row">
        <div class="col-lg-6 col-md-7 col-12">
            <div class="alert alert-info">
                <div>Здесь вы можете сообщить об оплате членских взносов или электричества без посещения Правления.</div>
                <div>Отметку об оплате в членской книжке можно будет проставить потом.</div>
            </div>
            <payment-form :prop-account='@json(new AccountResource(lc::account()))'
                          :prop-user='@json(new UserResource(lc::user()))'
                          :prop-invoice='@json($invoice ? new InvoiceResource($invoice) : null)'
            />
        </div>
    </div>
@endsection