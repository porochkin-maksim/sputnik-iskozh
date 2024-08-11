<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Illuminate\Support\Facades\Route;

$allowRobots = Route::is(RouteNames::INDEX)
    || Route::is(RouteNames::CONTACTS)
    || Route::is(RouteNames::GARBAGE)
    || Route::is(RouteNames::FILES);

?>
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">

    @if ($allowRobots)
        <meta name="robots"
              content="index, nofollow, noarchive">
    @else
        <meta name="robots"
              content="noindex">
    @endif

    <link type="image/x-icon"
          rel="shortcut icon"
          href="{{ url('/favicon.ico') }}">
    <link type="image/png"
          sizes="16x16"
          rel="icon"
          href="{{ url('/favicon16.png') }}">
    <link type="image/png"
          sizes="32x32"
          rel="icon"
          href="{{ url('/favicon32.png') }}">
    <link type="image/png"
          sizes="96x96"
          rel="icon"
          href="{{ url('/favicon96.png') }}">
    <link type="image/png"
          sizes="120x120"
          rel="icon"
          href="{{ url('/favicon120.png') }}">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <meta name="description"
          content="Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь">

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
</body>
</html>
