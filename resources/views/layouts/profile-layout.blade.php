<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Domains\Access\Enums\PermissionEnum;
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
</head>
<body class="d-flex flex-column h-100 {{ $season }} home"
      id="app"
      style="background-image: url('{{ $bgImage->getUrl() }}')">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom"
     id="topNavBar">
    <div class="container-fluid main px-3">
        <a class="navbar-brand"
           href="{{ route(RouteNames::HOME) }}">
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
            @include('layouts.partial.profile-nav')
        </div>
    </div>
</nav>
@if(!App::isProduction()) <div style="background-color: red;height:5px;z-index:99999;" class="position-absolute w-100 top-0 left-0"></div> @endif
<main class="px-3 py-2">
    @yield(SectionNames::CONTENT)
</main>
<footer class="w-100">
    <div class="d-flex justify-content-center py-3">

    </div>
</footer>
<alerts-block :disable-errors-popup="true"></alerts-block>
</body>
</html>
