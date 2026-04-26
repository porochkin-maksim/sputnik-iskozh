<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\Iframes;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;
use Illuminate\Support\Facades\Route;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Об организации вывоза мусора');
$openGraph->setUrl(route(RouteNames::GARBAGE));

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::GARBAGE) }}
    @if(lc::roleDecorator()->isSuperAdmin() && false)
        <page-editor :template="'public.garbage'"></page-editor>
    @endif
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <div>
        <div>
            <p>
                Место сбора для вывоза мусора для {{ config('app.name') }} находится справа от дороги по пути ко 2 участку.<br />
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
            <b class="text-danger">только</b> {{ config('app.name') }}
        </div>
    </div>

    {!! Iframes::garbagePlace() !!}
@endsection
