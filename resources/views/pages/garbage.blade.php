<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\Iframes;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;
use Illuminate\Support\Facades\Route;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::GARBAGE));

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical" href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    @if(app::roleDecorator()->canEditTemplates())
        <page-editor :template="'{{ ViewNames::PAGES_GARBAGE }}'"></page-editor>
    @endif
    <h1 class="border-bottom">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <div>
        <div>
            <p>
                Место сбора для вывоза мусора для {{ env('APP_NAME') }} находится справа от дороги по пути ко 2 участку.<br />
                Там установлены специальные контейнеры для складирования мусора.
            </p>
            <p>
                3 контейнера предназначены для вывоза только бытовых отходов (как дома под раковиной).
                Если сверху контейнера будет лежать шкаф, окно, дверь или покрышки и т.п., то ТСАХ может отказаться вывозить такой контейнер.
            </p>
        </div>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> У ТСАХ нет услуги подбора мусора. Все мешки надо выбрасывать в контейнер.
        </div>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Для крупногабаритного мусора стоит отдельный контейнер по пути ближе к правлению
        </div>
        <div class="alert alert-warning">
            <i class="fa fa-warning"></i> Место предназначено для сбора мусора
            <b class="text-danger">только</b> {{ env('APP_NAME') }}
        </div>
    </div>

    {!! Iframes::garbagePlace() !!}
@endsection