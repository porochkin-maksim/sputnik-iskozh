<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::PAYMENT));

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
                <div>Здесь вы можете сообщить об оплате членских взносов или электричества без посещения Правления.</div>
                <div>Отметку об оплате в членской книжке можно будет проставить потом.</div>
            </div>
            <payment-form></payment-form>
        </div>
    </div>
@endsection