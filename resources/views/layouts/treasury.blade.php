<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\Images\StaticFileLocator;

?>
        <!doctype html>
<html lang="{{ str_replace('_', '-', config('app.locale')) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield(SectionNames::TITLE, config('app.name') . ' — Касса')</title>

    @vite(['resources/sass/treasury/app.scss', 'resources/js/treasury.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
</head>
<body id="app">
<header class="treasury-header">
    <div class="treasury-header__brand">
        <div class="treasury-header__logo"
             style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')">
        </div>
        <span class="treasury-header__title">Касса</span>
    </div>
    <nav class="treasury-header__nav">
        <span class="treasury-header__user">{{ lc::userDecorator()->getShortName() }}</span>
        <a href="{{ route('logout') }}" class="treasury-header__logout">Выйти</a>
    </nav>
</header>

<main class="treasury-main">
    @yield(SectionNames::CONTENT)
</main>
<footer>
    <a href="{{ route(RouteNames::ADMIN) }}" class="treasury-footer__link">На главную</a>
</footer>
</body>
</html>
