<?php declare(strict_types=1);

use Core\Objects\ObjectsLocator;
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
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand"
               href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler d-none"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse"
                 id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
{{--                        <li class="nav-item">--}}
{{--                            <auth-block></auth-block>--}}
{{--                        </li>--}}
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown"
                               class="nav-link dropdown-toggle"
                               href="#"
                               role="button"
                               data-bs-toggle="dropdown"
                               aria-haspopup="true"
                               aria-expanded="false"
                               v-pre>
                                {{ $userDecorator->getDisplayName() }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end"
                                 aria-labelledby="navbarDropdown">
                                <a class="dropdown-item"
                                   href="{{ route('logout') }}">
                                    {{ 'Выйти' }}
                                </a>

                                <form id="logout-form"
                                      action="{{ route('logout') }}"
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

    <main class="py-4">
        @yield('content')
    </main>
</div>
</body>
</html>
