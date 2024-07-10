<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Illuminate\Support\Facades\Route;

?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if (Route::is(RouteNames::INDEX) || Route::is(RouteNames::CONTACTS))
        <meta name="robots" content="index, nofollow, noarchive">
    @else
        <meta name="robots" content="noindex">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь">

    <title>@yield(SectionNames::TITLE, RouteNames::name(Route::current()?->getName(), env('APP_NAME')))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
    @yield(SectionNames::METRICS)
</head>
<body>
<div id="app">
    <div class="d-flex justify-content-between flex-column min-vh-100">
        <div>
            <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top border-bottom" id="topNavBar">
                <div class="container-fluid main px-3">
                    <a class="navbar-brand"
                       href="{{ url('/') }}">
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
            <nav class="navbar sub-nav navbar-expand-lg navbar-light border-bottom d-lg-flex d-none" style="top:-1px;">
                <div class="container-fluid main px-3">
                    @include('layouts.partial.sub-nav')
                </div>
            </nav>
            <main class="px-3 py-2">
                @yield(SectionNames::CONTENT)
            </main>
        </div>
        <footer class="border-top bg-light">
            <div class="d-flex justify-content-center py-3">
                <div class="social">
                    <a class="social-link text-primary"
                       target="_blank"
                       href="https://t.me/SputnikIskozh">
                        <i class="fa fa-telegram"></i> Канал Telegram
                    </a>
                    <a class="social-link text-success"
                       target="_blank"
                       href="https://chat.whatsapp.com/ET8X52yidq0BmKq9WrKtqb">
                        <i class="fa fa-whatsapp"></i> Группа WhatsUp
                    </a>
                    <a class="social-link text-success"
                       target="_blank"
                       href="https://chat.whatsapp.com/Hfo9oClfdR6BX0dYs7VnG2">
                        <i class="fa fa-whatsapp"></i> Чат WhatsUp
                    </a>
                    <a class="social-link text-primary"
                       target="_blank"
                       href="https://vk.com/sputnik.iskozh">
                        <i class="fa fa-vk"></i> Группа ВК
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>
</body>
</html>
