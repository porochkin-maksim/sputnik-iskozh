<?php

use Core\Env;

?>

    <!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <meta name="robots"
          content="all">
    @stack('meta')

    <link rel="shortcut icon"
          type="image/x-icon"
          href="/favicon.ico" />

    <title>@yield('title', Env::appName())</title>

    @includeIf('hosts.frontend.mertika', [App::isProduction()])

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
    @stack('styles')
</head>
<body class="body" id="app">
<div class="main-container">
    <article id="app">
        @yield('app.content')
    </article>
</div>
</body>
</html>
