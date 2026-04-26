<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\OptionService;
use Core\Domains\StateSchedule\StateSchedule;
use Core\Shared\Helpers\Phone\PhoneHelper;
use App\Resources\RouteNames;
use App\Resources\Views\Iframes;
use App\Resources\Views\SectionNames;
use App\Services\Images\StaticFileLocator;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Контакты, режим работы');
$openGraph->setUrl(route(RouteNames::CONTACTS));

$month    = Carbon::now()->month;
$isWinter = $month >= 11 || $month <= 3;

/**
 * @var SntAccounting $accountingData
 * @var ChairmanInfo  $chairmanData
 */
$optionService  = app(OptionService::class);
$accountingData = $optionService->getByType(OptionEnum::SNT_ACCOUNTING)->getData();
$chairmanData   = $optionService->getByType(OptionEnum::CHAIRMAN_INFO)->getData();
$schedule       = StateSchedule::getScheduledDates(Carbon::now(), $isWinter ? 5 : 10);

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
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th colspan="2">Садоводческое Некоммерческое Товарищество "Спутник-Искож"</th>
        </tr>
        @if($chairmanData->getEmail())
            <tr>
                <th>Почта</th>
                <td>
                    <div>
                        <a href="mailto:{{ $chairmanData->getEmail() }}"><i class="fa fa-envelope-o"></i> {{ $chairmanData->getEmail() }}
                        </a>
                    </div>
                </td>
            </tr>
        @endif
        <tr>
            <th>Председатель</th>
            <td>
                <div>
                    {{ $chairmanData->getFullName() }}
                </div>
                @if($chairmanData->getPhone())
                    <div>
                        <a href="tel:{{ PhoneHelper::getPhoneNumberAsInternational($chairmanData->getPhone()) }}"><i class="fa fa-phone"></i> {{ $chairmanData->getPhone() }}
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
        <tr>
            <td colspan="2">
                <div class="d-flex justify-content-center">
                    <div class="social social-contacts d-flex">
                        @include('layouts.partial.social')
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <state-schedule :schedule='@json($schedule)'/>
            </td>
        </tr>
        <tr>
            <th>ОГРН</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="{{ $accountingData->getOgrn() }}">
                    {{ $accountingData->getOgrn() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>ИНН</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="{{ $accountingData->getInn() }}">
                    {{ $accountingData->getInn() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>КПП</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="{{ $accountingData->getKpp() }}">
                    {{ $accountingData->getKpp() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>Юридический адрес</th>
            <td>170533, Тверская область, Калининский район, деревня Пищалкино, тер. снт Спутник-Искож</td>
        </tr>
        <tr>
            <th colspan="2">&nbsp;</th>
        </tr>
        <tr>
            <th colspan="2">Банковские реквизиты</th>
        </tr>
        <tr>
            <th>Банк</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy='{{ $accountingData->getBank() }}'>
                    {{ $accountingData->getBank() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>Счёт</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy='{{ $accountingData->getAcc() }}'>
                    {{ $accountingData->getAcc() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>Корр.счёт</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy='{{ $accountingData->getCorr() }}'>
                    {{ $accountingData->getCorr() }}
                </a>
            </td>
        </tr>
        <tr>
            <th>БИК</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy='{{ $accountingData->getBik() }}'>
                    {{ $accountingData->getBik() }}
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="alert alert-info d-inline-block">
                    В назначении платежа указывайте <strong class="text-danger">номер дачи</strong> и
                    <strong class="text-danger">участок</strong>
                </div>
                <div>
                    <a href="{{ StaticFileLocator::StaticFileService()->qrPayment()->getUrl() }}"
                       data-lightbox="qr_payment">
                        <img src="{{ StaticFileLocator::StaticFileService()->qrPayment()->getUrl() }}"
                             style="width:200px;height:200px"
                             alt="QR код">
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">&nbsp;</th>
        </tr>
        <tr>
            <th colspan="2">Дополнительно</th>
        </tr>
        <tr>
            <th>Кадастровая карта</th>
            <td>
                <a href="https://nspd.gov.ru/map?thematic=PKK&zoom=16.295267300412462&coordinate_x=4009573.271111887&coordinate_y=7732190.873764123&baseLayerId=235&theme_id=1&is_copy_url=true&active_layers=36048&is_copy_url=true" target="_blank"><i class="fa fa-map-marker"></i> https://nspd.gov.ru/map</a>
            </td>
        </tr>
        <tr>
            <th>Горячая линия Россети</th>
            <td><a href="tel:88002200220"><i class="fa fa-phone"></i> 8(800)220-02-20</a></td>
        </tr>
        </tbody>
    </table>

    {!! Iframes::map() !!}

    <div class="mt-3">
        @include('public.partial.requests')
    </div>
@endsection
