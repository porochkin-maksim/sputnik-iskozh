<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Session\CookieNames;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partial.meta')
    @include('layouts.partial.favicon')

    <meta name="description" content="Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь">

    <title>@yield(SectionNames::TITLE, RouteNames::name(Route::current()?->getName(), env('APP_NAME')))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
    @yield(SectionNames::METRICS)
</head>
<body class="overflow-x-hidden">
<div id="app">
    <div class="d-flex justify-content-between flex-column min-vh-100">
        <div>
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
            <nav class="navbar sub-nav navbar-expand-lg navbar-light border-bottom d-lg-flex d-none"
                 style="top:-1px;">
                <div class="container-fluid main px-3">
                    @include('layouts.partial.sub-nav')
                </div>
            </nav>
            <main class="px-3 py-2">
                @yield(SectionNames::CONTENT)
            </main>
        </div>
        <footer class="border-top">
            <div class="d-flex justify-content-center py-3">
                <div class="social d-flex flex-column align-items-center">
                    @include(ViewNames::PARTIAL_SOCIAL)
                </div>
            </div>
        </footer>
    </div>
</div>
@if (!Cookie::has(CookieNames::COOKIE_AGREEMENT))
    <div class="d-block alert alert-info w-100 sticky-bottom text-center m-0"
         id="cookie">
        Сайт использует cookie, потому что без них не работает ни один сайт в интернете
        <button class="btn btn-success ms-2"
                type="submit"
                onclick="event.preventDefault();axios.post('<?= route(RouteNames::COOKIE_AGREEMENT) ?>', {}, $('#cookie').remove())">
            Прекрасно
        </button>
    </div>
@endif
</body>
</html>
