<?php declare(strict_types=1);

use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\OptionLocator;
use Core\Services\Images\StaticFileLocator;

/**
 * @var bool $showSigns
 */
$showSigns = $showSigns ?? false;

$signatureFile = StaticFileLocator::StaticFileService()->signatureDirector();
$stampFile     = StaticFileLocator::StaticFileService()->stampSnt();
/** @var ChairmanInfo $chairmanInfo */
$chairmanInfo = OptionLocator::OptionService()->getByType(OptionEnum::CHAIRMAN_INFO)->getData();

$signatureBase64 = $signatureFile->getBase64();
$stampBase64     = $stampFile->getBase64();

$signatureWidth  = '50mm';
$signatureHeight = '20mm';
$stampHeight     = '50mm';
$stampWidth      = $showSigns ? '50mm' : '0mm';
?>
<div style="width: 100%; margin-top: 5mm; text-align: right; vertical-align: top;">
    <!-- Таблица для подписи и ФИО на одной линии -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Ячейка с подписью (слева) -->
            <td style="width: auto; border-bottom: 1px solid #000; padding-bottom: 0; vertical-align: bottom; position: relative">
                <div style="margin-top: -5mm; margin-left: 10mm;position: absolute;">
                    @if($showSigns)
                        <img src="{{ $signatureBase64 }}" alt="подпись"
                             style="width: {{ $signatureWidth }}; height: {{ $signatureHeight }}; display: block;">
                    @endif
                </div>
            </td>
            <!-- Ячейка с ФИО (справа, занимает всё оставшееся место) -->
            <td style="width: 100%; border-bottom: 1px solid #000; padding-bottom: 0; text-align: right; vertical-align: bottom;">
                <span style="font-style: italic; background: white; padding-left: 10px;">
                    / @if($showSigns)
                        {{ $chairmanInfo->getShortName() }}
                    @else
                        {!! str_repeat('&nbsp;', 50) !!}
                    @endif
                </span>
            </td>
        </tr>
    </table>
    <!-- Печать ниже -->
    <div style="margin-top: -10mm;margin-right: 30mm">
        <img src="{{ $stampBase64 }}" alt="печать"
             style="width: {{ $stampWidth }}; height: {{ $stampHeight }}; display: block;">
    </div>
</div>
