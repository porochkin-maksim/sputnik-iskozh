<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::REQUESTS));

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::REQUESTS) }}
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <div class="row requests-block">
        <div class="col-lg-4 col-md-6 col-12">
            <a class="card request-item d-flex align-items-center justify-content-center p-3"
               href="{{ route(RouteNames::REQUESTS_PROPOSAL) }}">
                <h3>{{ RouteNames::name(RouteNames::REQUESTS_PROPOSAL) }}</h3>
                <div class="text-center">
                    Отправить идею или предложение по поводу улучшения жизни в СНТ
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
            <a class="card request-item d-flex align-items-center justify-content-center p-3"
               href="{{ route(RouteNames::REQUESTS_PAYMENT) }}">
                <h3>{{ RouteNames::name(RouteNames::REQUESTS_PAYMENT) }}</h3>
                <div class="text-center">
                    Сообщить о платеже
                </div>
            </a>
        </div>
        <div class="col-lg-4 col-md-6 col-12">
            <a class="card request-item d-flex align-items-center justify-content-center p-3"
               href="{{ route(RouteNames::REQUESTS_COUNTER) }}">
                <h3>{{ RouteNames::name(RouteNames::REQUESTS_COUNTER) }}</h3>
                <div class="text-center">
                    Отправить показания электроэнергии
                </div>
            </a>
        </div>
    </div>
@endsection