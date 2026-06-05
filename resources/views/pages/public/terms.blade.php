<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::TERMS) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => route(RouteNames::TERMS),
            'text' => RouteNames::name(RouteNames::TERMS),
        ])
        <div class="page-hero__lead">
            Условия использования сайта и размещённых сервисов.
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <div class="mb-0">
            <p>Настоящие условия определяют порядок использования сайта и размещённых на нём сервисов.</p>
            <ul class="mb-0">
                <li>Сайт предоставляет информационные сервисы и сервисы подачи обращений.</li>
                <li>При отправке форм следует указывать достоверные данные.</li>
                <li>Оператор вправе обновлять функциональность и содержание сайта.</li>
                <li>Актуальная редакция условий размещается на данной странице.</li>
                <li>По вопросам работы сайта используйте контакты, указанные на странице «Контакты».</li>
            </ul>
        </div>
    </div>
@endsection
