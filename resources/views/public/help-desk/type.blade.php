<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum $type
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name())
    ->setUrl(route(RouteNames::HELP_DESK_TYPE, $type->code()))
;

$categories = HelpDeskServiceLocator::TicketCategoryService()->getByType($type);
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::TITLE)
    {{ $openGraph->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::HELP_DESK_TYPE, $type) }}

    <h1 class="page-title">
        <a href="{{ $openGraph->getUrl() }}" class="text-decoration-none">
            <i class="fa {{ $type->icon() }} text-{{ $type->color() }} me-1"></i>
            {{ $type->name() }}
        </a>
    </h1>

    @include('public.help-desk.partial.categories-list', [
        'categories' => $categories,
        'category'   => null,
        'type'       => $type,
    ])
@endsection