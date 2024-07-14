<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::CONTACTS));

?>

@extends(ViewNames::LAYOUTS_ONE_COLUMN)

@push(SectionNames::META)
    <link rel="canonical" href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::MAIN)
    <table class="table mb-0">
        <tbody>
        <tr>
            <th colspan="2">Садоводческое Некоммерческое Товарищество "Спутник-Искож"</th>
        </tr>
        <tr>
            <th>ОГРН</th>
            <td>1026900580057</td>
        </tr>
        <tr>
            <th>ИНН</th>
            <td>6924004223</td>
        </tr>
        <tr>
            <th>КПП</th>
            <td>694901001</td>
        </tr>
        <tr>
            <th>Юридический адрес</th>
            <td>170533, Тверская область, Калининский район, деревня Пищалкино, тер. снт Спутник-Искож</td>
        </tr>
        </tbody>
    </table>

    <div class="my-2">
        <a class="btn btn-link px-1"
           href="https://egrp365.org/map/?kadnum=69:10:0205201:521"
           target="_blank">
            <i class="fa fa-external-link"></i> Кадастровая карта
        </a>
    </div>
    {{-- https://yandex.ru/map-constructor/?um=constructor:604bc906b7b7a8a4780c38eb81e9d38d066280cd260fe0139c348e4c74c4602a --}}
    {{-- <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A604bc906b7b7a8a4780c38eb81e9d38d066280cd260fe0139c348e4c74c4602a&amp;width=100%25&amp;height=547&amp;lang=ru_RU&amp;scroll=true"></script> --}}
    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A604bc906b7b7a8a4780c38eb81e9d38d066280cd260fe0139c348e4c74c4602a&amp;source=constructor"
            width="100%"
            height="570"
            frameborder="0"></iframe>
@endsection