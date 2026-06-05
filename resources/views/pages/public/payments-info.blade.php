<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PAYMENTS_INFO) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => route(RouteNames::PAYMENTS_INFO),
            'text' => RouteNames::name(RouteNames::PAYMENTS_INFO),
        ])
        <div class="page-hero__lead">
            Порядок приёма, идентификации и отражения платежей.
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <div class="mb-0">
            <p>На странице приведена информация о приёме и учёте платежей.</p>
            <ul class="mb-0">
                <li>Платежи учитываются после поступления данных в учётную систему.</li>
                <li>При оплате следует указывать корректные идентификаторы: номер участка, счёта или период.</li>
                <li>Срок зачисления и отражения платежа может зависеть от банка и платёжного сервиса.</li>
                <li>При ошибочном платеже или расхождении суммы направьте обращение через форму на сайте.</li>
                <li>Для уточнения платежей используйте контакты, указанные на странице «Контакты».</li>
            </ul>
        </div>
    </div>
@endsection
