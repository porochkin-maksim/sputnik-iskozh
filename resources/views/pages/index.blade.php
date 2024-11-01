<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <meta name="keywords" content="снт спутник-искож тверь сайт">
    <link rel="canonical" href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <index-page></index-page>
@endsection