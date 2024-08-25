<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\Iframes;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::CONTACTS));

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical"
          href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    @if(app::roleDecorator()->canEditTemplates())
        <page-editor :template="'{{ ViewNames::PAGES_CONTACTS }}'"></page-editor>
    @endif
    <h1 class="border-bottom">
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
        <tr>
            <th>01 апреля - 31 октября</th>
            <td>Каждые четверг и воскресенье 12:00-14:00</td>
        </tr>
        <tr>
            <th>01 ноября - 31 марта</th>
            <td>Каждые 1-ое и 3-е воскресенье месяца 12:00-14:00</td>
        </tr>
        <tr>
            <th colspan="2">&nbsp;</th>
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

    <div class="social d-flex flex-column py-3">
        @include(ViewNames::PARTIAL_SOCIAL)
    </div>

    <div class="my-2">
        <a class="btn btn-link px-1"
           href="https://egrp365.org/map/?kadnum=69:10:0205201:521"
           target="_blank">
            <i class="fa fa-external-link"></i> Кадастровая карта
        </a>
    </div>

    {!! Iframes::map() !!}
@endsection