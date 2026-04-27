<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum    $type
 * @var TicketCategoryDTO $category
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name() . ' | ' . $category->getName())
    ->setUrl(route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $category->getCode()]))
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
    {{ Breadcrumbs::render(RouteNames::HELP_DESK_CATEGORY, $type, $category) }}

    <h1 class="page-title">
        <a href="{{ $openGraph->getUrl() }}" class="text-decoration-none">
            <i class="fa {{ $type->icon() }} text-{{ $type->color() }} me-1"></i>
            {{ $type->name() }}
        </a>
    </h1>

    @include('public.help-desk.partial.categories-list', [
        'categories' => $categories,
        'category'   => $category,
        'type'       => $type,
    ])
@endsection