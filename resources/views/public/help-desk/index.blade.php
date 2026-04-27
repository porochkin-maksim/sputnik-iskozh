<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;
use Illuminate\Support\Facades\Route;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK))
    ->setUrl(route(RouteNames::HELP_DESK))
;

$items = [];
foreach (TicketTypeEnum::cases() as $ticketType) {
    $categories = HelpDeskServiceLocator::TicketCategoryService()->getByType($ticketType);

    if ($categories->hasServices()) {
        $items[] = [
            'href'  => route(RouteNames::HELP_DESK_TYPE, $ticketType->code()),
            'title' => $ticketType->name(),
            'icon'  => $ticketType->icon(),
            'color' => $ticketType->color(),
        ];
    }
}
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::TITLE)
    {{ $openGraph->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::HELP_DESK) }}
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    @include('public.partial.requests-grid', ['items' => $items])
@endsection