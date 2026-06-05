<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Invoices\InvoiceResource;
use App\Http\Resources\Profile\Users\UserResource;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS_PAYMENT));

/** @var null|InvoiceEntity $invoice */
?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::REQUESTS_PAYMENT) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Оплата и подтверждение платежа без визита в правление.
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-7 col-12">
            <div class="page-section page-card p-3 p-lg-4">
                <div class="alert alert-info">
                    <div>Здесь вы можете сообщить об оплате членских взносов или электричества без посещения Правления.</div>
                    <div>Отметку об оплате в членской книжке можно будет проставить потом.</div>
                </div>
                <payment-form :prop-account='@json(new AccountResource(lc::account()))'
                              :prop-user='@json(new UserResource(lc::user()))'
                              :prop-invoice='@json($invoice ? new InvoiceResource($invoice) : null)'
                ></payment-form>
            </div>
        </div>
    </div>
@endsection
