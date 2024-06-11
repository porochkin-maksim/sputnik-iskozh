<?php declare(strict_types=1);

use Core\Objects\ObjectsLocator;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Auth;

$user          = Auth::user();
$userDecorator = ObjectsLocator::Users()->UserDecorator($user);

?>
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <meta name="robots"
          content="noindex">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>@yield('title', env('APP_NAME'))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
    @stack('scripts')
</head>
<body>
<div id="app">
    <div class="d-flex justify-content-between flex-column min-vh-100">
        <div>
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid px-4 main">
                    <a class="navbar-brand"
                       href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    <button class="navbar-toggler"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent"
                            aria-expanded="false"
                            aria-label="Переключатель навигации">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse"
                         id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            {{--                    <li class="nav-item dropdown">--}}
                            {{--                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
                            {{--                            Выпадающий список--}}
                            {{--                        </a>--}}
                            {{--                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">--}}
                            {{--                            <li><a class="dropdown-item" href="#">Действие</a></li>--}}
                            {{--                            <li><a class="dropdown-item" href="#">Другое действие</a></li>--}}
                            {{--                            <li><hr class="dropdown-divider"></li>--}}
                            {{--                            <li><a class="dropdown-item" href="#">Что-то еще здесь</a></li>--}}
                            {{--                        </ul>--}}
                            {{--                    </li>--}}
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="{{ route(RouteNames::NEWS) }}">Новости</a>
                            </li>
{{--                            <li class="nav-item">--}}
{{--                                <a class="nav-link"--}}
{{--                                   href="{{ route(RouteNames::REPORTS) }}">Отчёты</a>--}}
{{--                            </li>--}}
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="{{ route(RouteNames::FILES) }}">Файлы</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="{{ Storage::url('Устав.pdf') }}"
                                   target="_blank">Устав</a>
                            </li>
                        </ul>

                        <ul class="navbar-nav ms-auto">
                            @guest
                                <li class="nav-item">
                                    <auth-block></auth-block>
                                </li>
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ $userDecorator->getDisplayName() }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route(RouteNames::HOME) }}">
                                            {{ 'Профиль' }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route(RouteNames::LOGOUT) }}">
                                            {{ 'Выйти' }}
                                        </a>

                                        <form id="logout-form"
                                              action="{{ route(RouteNames::LOGOUT) }}"
                                              method="POST"
                                              class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
        <footer class="border-top bg-light">
            <div class="d-flex justify-content-center py-3">
                <div class="social">
                    <a class="social-link text-primary" target="_blank" href="https://t.me/SputnikIskozh">
                        <i class="fa fa-telegram"></i> Канал Telegram
                    </a>
                    <a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/ET8X52yidq0BmKq9WrKtqb">
                        <i class="fa fa-whatsapp"></i> Группа WhatsUp
                    </a>
                    <a class="social-link text-success" target="_blank" href="https://chat.whatsapp.com/Hfo9oClfdR6BX0dYs7VnG2">
                        <i class="fa fa-whatsapp"></i> Чат WhatsUp
                    </a>
                </div>
            </div>
        </footer>
    </div>
</div>
</body>
</html>
