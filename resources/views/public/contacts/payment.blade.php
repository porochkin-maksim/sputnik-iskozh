<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Invoices\InvoiceResource;
use App\Http\Resources\Profile\Users\UserResource;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS_PAYMENT));

$invoiceUid = request()->get('invoice') ? UidFacade::findReferenceId(request()->get('invoice'), UidTypeEnum::INVOICE) : null;
$invoice    = null;
if ($invoiceUid) {
    $invoice = app(InvoiceService::class)->getById($invoiceUid);
    if ($invoice) {
        $period = app(PeriodService::class)->getById($invoice->getPeriodId());
        $invoice->setPeriod($period);
        $account = $invoice->getAccountId() === lc::account()->getId()
            ? lc::account()
            : app(AccountService::class)->getById($invoice->getAccountId());
        $invoice->setAccount($account);
    }
}
?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
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
