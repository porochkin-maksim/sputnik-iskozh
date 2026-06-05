<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::SEARCH));
$openGraph->setDescription('Поиск по сайту');

?>

@extends('layouts.app-layout')

@push(SectionNames::META)
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::SEARCH) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => route(RouteNames::SEARCH),
            'text' => RouteNames::name(RouteNames::SEARCH),
        ])
        <div class="page-hero__lead">
            Поиск по сайту и документам.
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <search-block></search-block>
    </div>
@endsection
