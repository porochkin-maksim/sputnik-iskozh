<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum           $type
 * @var TicketCategoryEntity     $category
 * @var TicketCategoryCollection $categories
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name() . ' | ' . $category->getName())
    ->setUrl(route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $category->getCode()]))
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
    {{ Breadcrumbs::render(RouteNames::HELP_DESK_CATEGORY, $type, $category) }}

    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'icon' => $type->icon(),
            'iconColor' => $type->color(),
            'text' => $type->name(),
        ])
        <div class="page-hero__lead">
            {{ $category->getName() }}
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        @include('partials.public.help-desk-categories-list', [
            'categories' => $categories,
            'category'   => $category,
            'type'       => $type,
        ])
    </div>
@endsection
