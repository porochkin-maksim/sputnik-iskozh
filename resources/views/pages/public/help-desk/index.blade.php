<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;
use Illuminate\Support\Facades\Route;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK))
    ->setUrl(route(RouteNames::HELP_DESK))
;

/** @var array<int, array{href:string,title:string,icon:string,color:string}> $items */
?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::TITLE)
    {{ $openGraph->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::HELP_DESK) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Выберите раздел для отправки обращения или просмотра справочной информации.
        </div>
    </div>
    <div class="page-section">
        @include('partials.public.requests-grid', ['items' => $items])
    </div>
@endsection
