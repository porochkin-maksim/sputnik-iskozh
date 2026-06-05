<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::COOKIE_POLICY) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => route(RouteNames::COOKIE_POLICY),
            'text' => RouteNames::name(RouteNames::COOKIE_POLICY),
        ])
        <div class="page-hero__lead">
            Использование cookie и сопутствующих технических данных.
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <div class="mb-0">
            <p>Сайт использует cookie-файлы для корректной работы отдельных функций.</p>
            <ul class="mb-0">
                <li>Технические cookie необходимы для работы отдельных функций сайта.</li>
                <li>Cookie могут использоваться для сохранения пользовательских настроек.</li>
                <li>Отключение cookie в браузере может привести к некорректной работе части функций.</li>
                <li>Продолжая использование сайта, пользователь соглашается с использованием cookie.</li>
            </ul>
        </div>
    </div>
@endsection
