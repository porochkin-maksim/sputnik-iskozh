<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS));

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection
@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::REQUESTS) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Все основные формы обращения и запросы собраны в одном месте.
        </div>
    </div>
    <div class="page-section">
        @include('partials.public.requests')
    </div>
@endsection
