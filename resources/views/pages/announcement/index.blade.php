<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::ANNOUNCEMENTS));

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <announcements-block></announcements-block>
@endsection