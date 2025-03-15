<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Session\CookieNames;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

$bgImage = StaticFileLocator::StaticFileService()->seasonBgImage();

$season = match (Carbon::now()->month) {
    3, 4, 5   => 'spring',
    6, 7, 8   => 'summer',
    9, 10, 11 => 'autumn',
    default   => 'winter',
};
?>
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partial.meta')
    @include('layouts.partial.favicon')

    <title>@yield(SectionNames::TITLE, RouteNames::name(Route::current()?->getName(), env('APP_NAME')))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
    @yield(SectionNames::METRICS)
</head>
<body class="d-flex flex-column h-100 {{ $season }}"
      id="app"
      style="background-image: url('{{ $bgImage->getUrl() }}')">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom"
     id="topNavBar">
    <div class="container-fluid main px-3">
        <a class="navbar-brand"
           href="{{ url('/') }}">
            <div class="logo"
                 style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')"></div>
            {{ env('APP_NAME') }}
        </a>
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#topMenuNavContent"
                aria-controls="topMenuNavContent"
                aria-expanded="false"
                aria-label="Переключатель навигации">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse"
             id="topMenuNavContent">
            @include('layouts.partial.nav')
        </div>
    </div>
</nav>
<main class="px-3 py-2">
    @yield(SectionNames::CONTENT)
</main>
<footer>
    <div class="d-flex justify-content-center py-3">
        <div class="social d-flex flex-column align-items-center">
            @include(ViewNames::PARTIAL_SOCIAL)
        </div>
    </div>
</footer>
<alerts-block :disable-errors-popup="true"></alerts-block>
{{--@if (!Cookie::has(CookieNames::COOKIE_AGREEMENT))--}}
{{--    <div class="d-block alert alert-info w-100 sticky-bottom text-center m-0"--}}
{{--         id="cookie">--}}
{{--        Сайт использует cookie, потому что без них не работает ни один сайт в интернете--}}
{{--        <button class="btn btn-success ms-2"--}}
{{--                type="submit"--}}
{{--                onclick="event.preventDefault();axios.post('<?= route(RouteNames::COOKIE_AGREEMENT) ?>', {}, $('#cookie').remove())">--}}
{{--            Прекрасно--}}
{{--        </button>--}}
{{--    </div>--}}
{{--@endif--}}
</body>
</html>
