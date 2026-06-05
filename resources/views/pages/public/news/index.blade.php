<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::NEWS));

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::NEWS) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Новости, объявления и важные сообщения для жителей СНТ.
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <news-block :current-page='@json($currentPage)'></news-block>
    </div>
@endsection
