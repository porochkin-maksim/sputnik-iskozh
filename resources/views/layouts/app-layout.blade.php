<?php declare(strict_types=1);

use Carbon\Carbon;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\Images\StaticFileLocator;
use App\Session\CookieNames;
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
<html lang="{{ str_replace('_', '-', config('app.locale')) }}">
<head>
    @include('layouts.partial.meta')
    @include('layouts.partial.favicon')

    <title>@yield(SectionNames::TITLE, RouteNames::name(Route::current()?->getName(), config('app.name')))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
    @yield(SectionNames::METRICS)
    @include('layouts.partial.access.import-roles')
</head>
<body class="d-flex flex-column h-100 {{ $season }} layout-background-image"
      id="app"
      style="background-image: url('{{ $bgImage->getUrl() }}')">
<nav class="navbar navbar-expand-lg navbar-dark site-navbar site-navbar--public sticky-top border-bottom"
     id="topNavBar">
    <div class="container-fluid main px-3">
        <a class="navbar-brand site-navbar__brand"
           href="{{ url('/') }}">
            <div class="logo layout-logo-image"
                 style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')"></div>
            {{ config('app.name') }}
        </a>
        <button class="navbar-toggler"
                type="button"
                data-navbar-target="#topMenuNavContent"
                data-navbar-toggle="collapse"
                aria-controls="topMenuNavContent"
                aria-expanded="false"
                aria-label="Переключатель навигации">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse site-navbar__collapse"
             id="topMenuNavContent">
            @include('layouts.partial.public-topnav')
        </div>
    </div>
</nav>
@if(!App::isProduction()) <div class="development-strip position-absolute w-100 top-0 left-0"></div> @endif
<main class="px-3 py-2 page-shell">
    <div class="site-subnav d-none d-lg-block">
        @include('layouts.partial.public-subnav')
    </div>
    @yield(SectionNames::CONTENT)
    <div class="d-flex justify-content-end mt-3">
        <share-button></share-button>
    </div>
</main>
<footer>
    <div class="d-flex justify-content-center py-3">
        <div class="social d-flex flex-column align-items-center">
            @include('layouts.partial.social')
            <div class="mt-2">
                @include('partials.public.legal-links')
            </div>
        </div>
    </div>
</footer>
<alerts-block :disable-errors-popup="true"></alerts-block>
@if (!Cookie::has(CookieNames::COOKIE_AGREEMENT))
    <div class="d-block alert alert-info w-100 sticky-bottom text-center m-0"
         id="cookie-banner">
        Сайт использует cookie для корректной работы сервиса.
        Подробнее:
        <a href="{{ route(RouteNames::COOKIE_POLICY) }}"
           class="alert-link">политика cookie</a>.
        <button class="btn btn-success ms-2"
                type="button"
                id="cookie-agreement-btn"
                data-endpoint="{{ route(RouteNames::COOKIE_AGREEMENT) }}">
            Принять
        </button>
    </div>
@endif
</body>
</html>
