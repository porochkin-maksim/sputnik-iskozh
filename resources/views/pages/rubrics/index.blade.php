<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::RUBRICS));

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical"
          href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <h1 class="border-bottom">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>

    <h3 class="text-center">Рейдерский захват СНТ: теория и практика</h3>
    <div class="d-flex justify-content-center w-100 mb-2">
        <iframe style="width:100%;max-width:1080px; height:60vh;max-height:720px"
                src="https://rutube.ru/play/embed/a4de5f32dbfbe657c6baf5a311251547"
                frameBorder="0"
                allow="clipboard-write; autoplay"
                webkitAllowFullScreen
                mozallowfullscreen
                allowFullScreen></iframe>
    </div>

    <div class="d-flex flex-column align-items-center w-100 mb-2">
        <p>
            <a href="https://www.youtube.com/watch?v=ZEHrSK8DVWs"
               target="_blank">
                <i class="fa fa-external-link"></i>&nbsp;Оригинал видео YouTube
            </a>
        </p>
        <p>
            <a href="https://dzen.ru/video/watch/63f5004a593b986e4c0f4662?f=video"
               target="_blank">
                <i class="fa fa-external-link"></i>&nbsp;Оригинал видео Dzen
            </a>
        </p>
    </div>
@endsection