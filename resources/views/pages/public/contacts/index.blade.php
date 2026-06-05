<?php declare(strict_types=1);

use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Shared\Helpers\Phone\PhoneHelper;
use App\Resources\RouteNames;
use App\Resources\Views\Iframes;
use App\Resources\Views\SectionNames;
use App\Services\Images\StaticFileLocator;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Контакты, режим работы');
$openGraph->setUrl(route(RouteNames::CONTACTS));

/**
 * @var SntAccounting $accountingData
 * @var ChairmanInfo  $chairmanData
 * @var array         $schedule
 */

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::CONTACTS) }}
    @if(lc::roleDecorator()->isSuperAdmin() && false)
        <page-editor :template="'public.contacts'"></page-editor>
    @endif
    
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Официальные контакты, реквизиты и сервисные ссылки.
        </div>
    </div>

    <div class="info-table page-section">
        <table class="table table-borderless align-middle mb-0">
            <tbody>
            <tr class="info-table__section">
                <th colspan="2">Садоводческое Некоммерческое Товарищество "Спутник-Искож"</th>
            </tr>
            @if($chairmanData->getEmail())
                <tr class="info-table__row">
                    <th>Почта</th>
                    <td>
                        <div class="info-table__value">
                            <a href="mailto:{{ $chairmanData->getEmail() }}"><i
                                        class="fa fa-envelope-o"></i> {{ $chairmanData->getEmail() }}
                            </a>
                        </div>
                    </td>
                </tr>
            @endif
            <tr class="info-table__row">
                <th>Председатель</th>
                <td>
                    <div class="info-table__value">
                        {{ $chairmanData->getFullName() }}
                    </div>
                    @if($chairmanData->getPhone())
                        <div class="info-table__value">
                            <a href="tel:{{ PhoneHelper::getPhoneNumberAsInternational($chairmanData->getPhone()) }}"><i
                                        class="fa fa-phone"></i> {{ $chairmanData->getPhone() }}
                            </a>
                        </div>
                    @endif
                </td>
            </tr>
            {{--        <tr>--}}
            {{--            <th>Электрик</th>--}}
            {{--            <td>--}}
            {{--                <div>--}}
            {{--                    Дмитрий--}}
            {{--                </div>--}}
            {{--                <div>--}}
            {{--                    <a href="tel:+79105330631"><i class="fa fa-phone"></i> +7(910)533-06-31</a>--}}
            {{--                </div>--}}
            {{--                <div>--}}
            {{--                    <a href="tel:+79607150046"><i class="fa fa-phone"></i> +7(960)715-00-46</a>--}}
            {{--                </div>--}}
            {{--            </td>--}}
            {{--        </tr>--}}
            <tr class="info-table__panel-row">
                <td colspan="2">
                    @include('partials.public.social-strip', ['class' => 'my-0'])
                </td>
            </tr>
            <tr class="info-table__panel-row">
                <td colspan="2">
                    <state-schedule :schedule='@json($schedule)'></state-schedule>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>ОГРН</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy="{{ $accountingData->getOgrn() }}">
                        {{ $accountingData->getOgrn() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>ИНН</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy="{{ $accountingData->getInn() }}">
                        {{ $accountingData->getInn() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>КПП</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy="{{ $accountingData->getKpp() }}">
                        {{ $accountingData->getKpp() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>Юридический адрес</th>
                <td>170533, Тверская область, Калининский район, деревня Пищалкино, тер. снт Спутник-Искож</td>
            </tr>
            <tr class="info-table__spacer">
                <th colspan="2"></th>
            </tr>
            <tr class="info-table__section">
                <th colspan="2">Банковские реквизиты</th>
            </tr>
            <tr class="info-table__row">
                <th>Банк</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy='{{ $accountingData->getBank() }}'>
                        {{ $accountingData->getBank() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>Счёт</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy='{{ $accountingData->getAcc() }}'>
                        {{ $accountingData->getAcc() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>Корр.счёт</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy='{{ $accountingData->getCorr() }}'>
                        {{ $accountingData->getCorr() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>БИК</th>
                <td>
                    <a class="link-firm cursor-pointer"
                       data-copy='{{ $accountingData->getBik() }}'>
                        {{ $accountingData->getBik() }}
                    </a>
                </td>
            </tr>
            <tr class="info-table__panel-row">
                <td colspan="2">
                    <div class="alert alert-info d-block text-center mb-3">
                        В назначении платежа указывайте <strong class="text-danger">номер дачи</strong> и
                        <strong class="text-danger">участок</strong>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="{{ StaticFileLocator::StaticFileService()->qrPayment()->getUrl() }}"
                           data-lightbox="qr_payment"
                           class="public-qr-card">
                            <img src="{{ StaticFileLocator::StaticFileService()->qrPayment()->getUrl() }}"
                                 class="qr-image-200"
                                 alt="QR код">
                        </a>
                    </div>
                </td>
            </tr>
            <tr class="info-table__spacer">
                <th colspan="2"></th>
            </tr>
            <tr class="info-table__section">
                <th colspan="2">Дополнительно</th>
            </tr>
            <tr class="info-table__row">
                <th>Кадастровая карта</th>
                <td>
                    <a href="https://nspd.gov.ru/map?thematic=PKK&zoom=16.295267300412462&coordinate_x=4009573.271111887&coordinate_y=7732190.873764123&baseLayerId=235&theme_id=1&is_copy_url=true&active_layers=36048&is_copy_url=true"
                       target="_blank"><i class="fa fa-map-marker"></i> https://nspd.gov.ru/map</a>
                </td>
            </tr>
            <tr class="info-table__row">
                <th>Горячая линия Россети</th>
                <td><a href="tel:88002200220"><i class="fa fa-phone"></i> 8(800)220-02-20</a></td>
            </tr>
            </tbody>
        </table>
    </div>

    {!! Iframes::map() !!}

    @include('partials.public.requests-section')
@endsection
