<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Counters\CounterResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Http\Resources\Shared\ResourseList;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS_COUNTER));
?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::REQUESTS_COUNTER) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Передача показаний без визита в правление.
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-7 col-12">
            <div class="page-section page-card p-3 p-lg-4">
                <div class="alert alert-info">
                    <div>Здесь вы можете сообщить текущие показания электричества без посещения Правления.</div>
                    <div>Отметку в членской книжке можно будет проставить потом.</div>
                </div>
                <counter-form :prop-account='@json(new AccountResource(lc::account()))'
                              :prop-user='@json(new UserResource(lc::user()))'
                              :prop-counters='@json(new ResourseList(lc::counters(), CounterResource::class))'></counter-form>
            </div>
        </div>
    </div>
@endsection
