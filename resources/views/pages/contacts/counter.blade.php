<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::COUNTER));

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <div class="row">
        <div class="col-lg-6 col-md-7 col-12">
            <div class="alert alert-info">
                <div>Здесь вы можете сообщить текущие показания электричества без посещения Правления.</div>
                <div>Отметку в членской книжке можно будет проставить потом.</div>
            </div>
            <counter-form></counter-form>
        </div>
    </div>
@endsection