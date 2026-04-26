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
    <search-block></search-block>
@endsection