<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Resources\RouteNames;
use Core\Resources\Views\Iframes;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Контакты, режим работы');
$openGraph->setUrl(route(RouteNames::CONTACTS));

$month    = Carbon::now()->month;
$isWinter = $month >= 11 || $month <= 3;
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    @if(lc::roleDecorator()->canEditTemplates())
        <page-editor :template="'{{ ViewNames::PAGES_CONTACTS }}'"></page-editor>
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
        <tr>
            <th>Председатель</th>
            <td>
                <div>
                    Крылов Алексей Владимирович
                </div>
                <div>
                    <a href="tel:+79001128403"><i class="fa fa-phone"></i> +7(900)112-84-03</a>
                </div>
            </td>
        </tr>
        <tr>
            <th>Электрик</th>
            <td>
                <div>
                    Дмитрий
                </div>
                <div>
                    <a href="tel:+79105330631"><i class="fa fa-phone"></i> +7(910)533-06-31</a>
                </div>
                <div>
                    <a href="tel:+79607150046"><i class="fa fa-phone"></i> +7(960)715-00-46</a>
                </div>
            </td>
        </tr>
        <tr>
            <th colspan="2">&nbsp;</th>
        </tr>
        <tr>
            <th colspan="2">График работы</th>
        </tr>
        <tr @if(!$isWinter) class="table-info" @endif>
            <th>01 апреля - 31 октября</th>
            <td>Каждые четверг и воскресенье 12:00-14:00</td>
        </tr>
        <tr @if($isWinter) class="table-info" @endif>
            <th>01 ноября - 31 марта</th>
            <td>Каждые 1-ое и 3-е воскресенье месяца 12:00-14:00</td>
        </tr>
        <tr>
            <th colspan="2">&nbsp;</th>
        </tr>
        <tr>
            <th>ОГРН</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="1026900580057">
                    1026900580057
                </a>
            </td>
        </tr>
        <tr>
            <th>ИНН</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="6924004223">
                    6924004223
                </a>
            </td>
        </tr>
        <tr>
            <th>КПП</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="694901001">
                    694901001
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
                   data-copy='ФИЛИАЛ "ЦЕНТРАЛЬНЫЙ" БАНКА ВТБ (ПАО)'>
                    ФИЛИАЛ "ЦЕНТРАЛЬНЫЙ" БАНКА ВТБ (ПАО)
                </a>
            </td>
        </tr>
        <tr>
            <th>Счёт</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="40703810017762000022">
                    40703810017762000022
                </a>
            </td>
        </tr>
        <tr>
            <th>Корр.счёт</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="30101810145250000411">
                    30101810145250000411
                </a>
            </td>
        </tr>
        <tr>
            <th>БИК</th>
            <td>
                <a class="link cursor-pointer text-decoration-none"
                   data-copy="044525411">
                    044525411
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
            <th>Горячая линия Россети</th>
            <td><a href="tel:88002200220"><i class="fa fa-phone"></i> 8(800)220-02-20</a></td>
        </tr>
        </tbody>
    </table>

    <h3>
        <a href="{{ route(RouteNames::PROPOSAL) }}"
           class="btn btn-sm btn-success">
            <i class="fa fa-envelope"></i>&nbsp;Написать&nbsp;предложение
        </a>
    </h3>

    <div class="my-2">
        <a class="btn btn-link px-1"
           href="https://egrp365.org/map/?kadnum=69:10:0205201:521"
           target="_blank">
            <i class="fa fa-external-link"></i> Кадастровая карта
        </a>
    </div>

    {!! Iframes::map() !!}
@endsection