<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\OptionLocator;
use Core\Enums\DateTimeFormat;
use Core\Services\Images\StaticFileLocator;

/**
 * @var null|InvoiceDTO        $invoice
 * @var null|PeriodDTO         $period
 * @var null|ServiceCollection $services
 * @var SntAccounting          $sntAccounting
 */
$invoice  = $invoice ?? null;
$period   = $period ?? $invoice->getPeriod(true);
$services = $services ?? new ServiceCollection();

$account = $invoice?->getAccount(true);
$claims  = $invoice?->getClaims(true)?->sortByServiceTypes();

$sntAccounting = OptionLocator::OptionService()->getByType(OptionEnum::SNT_ACCOUNTING)->getData();

$blank = '_________';
?>
        <!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Квитанция на оплату</title>
    @include('exports.documents.styles.common')
    <style>
        .details {
            margin-bottom : 20px;
        }

        .details th {
            text-align : left;
            width      : 1%;
        }

        .items thead th {
            text-align : center;
        }

        .items tbody tr td:nth-child(n+2) {
            text-align : right;
        }
    </style>
</head>
<body>
<div class="header">
    @if($invoice)
        <h1>Квитанция на оплату по счёту № {{ $invoice->getId() }} от {{ $invoice->getUpdatedAt()->format('d.m.Y') }}</h1>
    @else
        <h1>Квитанция на оплату № {{ $blank }} от {{ $blank . $blank }}</h1>
    @endif
</div>

<div class="details">
    <table class="table-bordered">
        <tr>
            <td colspan="2" style="text-align: center"><b>Получатель</b></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><b>Садоводческое некоммерческое товарищество «Спутник-Искож»</b>
            </td>
        </tr>
        <tr>
            <th>ИНН/КПП:</th>
            <td>{{ $sntAccounting->getInn() }} / {{ $sntAccounting->getKpp() }}</td>
        </tr>
        <tr>
            <th>Р/С:</th>
            <td>{{ $sntAccounting->getAcc() }}</td>
        </tr>
        <tr>
            <th>К/С:</th>
            <td>{{ $sntAccounting->getCorr() }}</td>
        </tr>
        <tr>
            <th>Банк:</th>
            <td>{{ $sntAccounting->getBank() }}</td>
        </tr>
        <tr>
            <th>БИК:</th>
            <td>{{ $sntAccounting->getBik() }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center"><b>Плательщик</b></td>
        </tr>
        <tr>
            <th>Участок:</th>
            @if ($account)
                <td>{{ $account->getNumber() }} (площадь {{ $account->getSize() }}м²)</td>
            @else
                <td></td>
            @endif
        </tr>
        <tr>
            <th>Период:</th>
            <td>
                {{ $period?->getStartAt()->format(DateTimeFormat::DATE_VIEW_FORMAT) ?: $blank }}
                –
                {{ $period?->getEndAt()->format(DateTimeFormat::DATE_VIEW_FORMAT) ?: $blank }}
            </td>
        </tr>
    </table>
</div>

<div class="items">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th></th>
            <th>Тариф</th>
            <th>Стоимость</th>
            <th>Оплачено</th>
            <th>Долг</th>
        </tr>
        </thead>
        <tbody>
        @if($claims)
            @foreach($claims as $claim)
                <tr>
                    <td>{{ $claim->getName() ?: $claim->getService(true)->getName() }}</td>
                    <td class="text-end text-nowrap">{{ number_format($claim->getTariff(), 2) }}</td>
                    <td class="text-end text-nowrap">{{ number_format($claim->getCost(), 2) }}</td>
                    <td class="text-end text-nowrap">{{ number_format($claim->getPaid(), 2) }}</td>
                    <td class="text-end text-nowrap">{{ number_format($claim->getDelta(), 2) }}</td>
                </tr>
            @endforeach
        @elseif($services)
            @foreach($services as $service)
                <tr>
                    <td>{{ $service->getName() }}</td>
                    <td class="text-end text-nowrap">{{ number_format($service->getCost(), 2) }}</td>
                    <td class="text-end text-nowrap"></td>
                    <td class="text-end text-nowrap"></td>
                    <td class="text-end text-nowrap"></td>
                </tr>
            @endforeach
        @endif
        @if($invoice)
            <tr>
                <th class="text-end" colspan="2">Итого</th>
                <td class="text-end text-nowrap">{{ number_format($invoice->getCost(), 2) }}</td>
                <td class="text-end text-nowrap">{{ number_format($invoice->getPaid(), 2) }}</td>
                <td class="text-end text-nowrap">{{ number_format($invoice->getDelta(), 2) }}</td>
            </tr>
        @else
            <tr>
                <th class="text-end" colspan="2">Итого</th>
                <td class="text-end text-nowrap"></td>
                <td class="text-end text-nowrap"></td>
                <td class="text-end text-nowrap"></td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div style="text-align: right; font-size: 1.2em; font-weight: bold; margin-top: 20px;">
    Итого к оплате: {{ $invoice ? number_format($invoice->getDelta(), 2) : ($blank . $blank . $blank) }}
</div>

@if(!$invoice || $invoice->getDelta())
    <div style="text-align: center; color: #770000; font-size: 1em; font-weight: bold;  font-style: italic; padding: 10px 5px; background: #ffcaca; margin-top: 10px;">
        При оплате ОБЯЗАТЕЛЬНО указывать номер ДАЧИ и номер УЧАСТКА
        @if($account)
            - "{{ $account->getNumber() }}"
        @endif
    </div>
@endif

<div style="margin-top: 10px;">
    <table>
        <tr>
            <td style="width: 33%; vertical-align: top;">
                <div style="display: inline-block;">
                    <img src="{{ StaticFileLocator::StaticFileService()->qrPayment()->getBase64() }}"
                         alt="QR-код"
                         style="width: 150px; height: 150px; display: block;">
                    <div style="font-style: italic; text-align: center">для оплат</div>
                </div>
            </td>
            <td>
                @include('exports.documents.signature', ['showSigns' => (bool) $invoice])
            </td>
        </tr>
    </table>

    <div style="font-style: italic; padding: 10px 5px;margin-top: 10px;">
        Для оплаты по QR-коду:
        <ol style="margin-top: 0">
            <li>откройте приложение Вашего банка</li>
            <li>выберите "сканировать QR-код"</li>
            <li>наведите камеру</li>
        </ol>
    </div>
</div>

@if ($invoice)
    <div style="margin-top: 20px; font-size: 0.9em; text-align: center;">
        Квитанция сгенерирована автоматически {{ Carbon::now()->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT) }}
    </div>
@endif
</body>
</html>
