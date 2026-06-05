<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PERSONAL_DATA_CONSENT) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => route(RouteNames::PERSONAL_DATA_CONSENT),
            'text' => RouteNames::name(RouteNames::PERSONAL_DATA_CONSENT),
        ])
        <div class="page-hero__lead">
            Текст согласия, который сопровождает формы сайта.
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <div class="mb-0">
            <p>Отправка форм на сайте означает согласие на обработку персональных данных.</p>
            <ul class="mb-0">
                <li>Обрабатываются данные, указанные в формах сайта.</li>
                <li>Цели обработки: рассмотрение обращений, обратная связь, исполнение обязанностей СНТ «Спутник-Искож».</li>
                <li>Обработка осуществляется в соответствии с действующим законодательством РФ.</li>
                <li>Согласие действует до достижения целей обработки либо до его отзыва.</li>
                <li>Запрос на отзыв согласия можно направить по контактам, указанным на странице «Контакты».</li>
            </ul>
        </div>
    </div>
@endsection
