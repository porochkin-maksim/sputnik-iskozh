<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Services\OpenGraph\Enums\OpenGraphType;
use Core\Services\OpenGraph\Models\OpenGraph;
use Illuminate\Support\Facades\Route;

$openGraph = new OpenGraph();
$openGraph->setType(OpenGraphType::WEBSITE)
    ->setTitle(RouteNames::name(Route::current()?->getName(), env('APP_NAME')))
    ->setUrl(route(RouteNames::CONTACTS))
    ->setImage(StaticFileLocator::StaticFilesService()->logoSnt()->getUrl())
    ->setDescription('Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь');

?>

@extends(ViewNames::LAYOUTS_ONE_COLUMN)

@push(SectionNames::META)
    <link rel="canonical" href="{{ route(RouteNames::GARBAGE) }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::MAIN)
    <h1>{{ RouteNames::name(Route::current()?->getName(), env('APP_NAME')) }}</h1>

    <div>
        <div>
            <p>
                Официальное место сбора для вывоза мусора для {{ env('APP_NAME') }} находится справа от дороги по пути ко 2 участку.<br/>
                Там установлены специальные контейнеры для складирования мусора.
            </p>
            <p>
                Контейнеры предназначены для вывоза только бытовых отходов (как дома под раковиной).
                Если сверху контейнера будет лежать шкаф, окно, дверь или покрышки и т.п., то ТСАХ может отказаться вывозить такой контейнер.
            </p>
        </div>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> У ТСАХ нет услуги подбора мусора. Все мешки надо выбрасывать в контейнер.
        </div>
        <div class="alert alert-warning">
            <i class="fa fa-warning"></i> Место предназначено для сбора мусора <b class="text-danger">только</b> {{ env('APP_NAME') }}
        </div>
    </div>

    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A604bc906b7b7a8a4780c38eb81e9d38d066280cd260fe0139c348e4c74c4602a&amp;source=constructor"
            width="100%"
            height="570"
            frameborder="0"></iframe>
@endsection