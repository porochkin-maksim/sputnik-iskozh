<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum           $type
 * @var TicketCategoryCollection $categories
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name())
    ->setUrl(route(RouteNames::HELP_DESK_TYPE, $type->code()))
;

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::TITLE)
    {{ $openGraph->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::HELP_DESK_TYPE, $type) }}

    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'icon' => $type->icon(),
            'iconColor' => $type->color(),
            'text' => $type->name(),
        ])
        <div class="page-hero__lead">
            Все разделы и услуги по выбранному типу обращения.
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        @include('partials.public.help-desk-categories-list', [
            'categories' => $categories,
            'category'   => null,
            'type'       => $type,
        ])
    </div>
@endsection
