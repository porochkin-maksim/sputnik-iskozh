<?php declare(strict_types=1);

use Core\Domains\Infra\HistoryChanges\Collections\HistoryChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\User\UserLocator;
use Core\Enums\DateTimeFormat;

/**
 * @var HistoryChangesCollection $historyChanges
 */
?>
        <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Document</title>
    @vite(['resources/sass/app.scss'])
    <style>
        html, body {
            font-size : 14px;
        }

        table {
            width : 100%;
        }

        table, th, td {
            border          : 1px solid black;
            border-collapse : collapse;
        }

        th {
            text-align       : center;
            background-color : #cfcfcf;
        }

        td, th {
            padding : 2px;
        }

        .description-cell {
            padding : 0;
        }

        .description-cell div {
            display       : flex;
            width         : 100%;
            flex          : 1;
            margin        : 0;
            border-radius : 0;
            padding       : 0.3rem
        }

        .description-cell table,
        .description-cell th,
        .description-cell td {
            font-weight   : normal;
            border-bottom : 0;
        }

        .description-cell table,
        .description-cell tr th:first-child,
        .description-cell tr td:first-child {
            border-left : 0;
        }

        .description-cell table,
        .description-cell tr th:last-child,
        .description-cell tr td:last-child {
            border-right : 0;
        }
    </style>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>Время</th>
        <th>Пользователь</th>
        <th>id1</th>
        <th>id2</th>
        <th>История</th>
    </tr>
    </thead>
    <tbody>
    @foreach($historyChanges as $historyChange)
        <tr>
            <td style="text-align: center; vertical-align: top;">
                {{ $historyChange->getId() }}
            </td>
            <td style="text-align: center; vertical-align: top; width:70px;">
                {{ $historyChange->getCreatedAt()->format(DateTimeFormat::DATE_VIEW_FORMAT) }}
            </td>
            <td style="text-align: center; vertical-align: top; width:70px;">
                {{ $historyChange->getCreatedAt()->format(DateTimeFormat::TIME_FULL) }}
            </td>
            <td style="text-align: center; vertical-align: top; white-space: nowrap;">
                {{ UserLocator::UserDecorator($historyChange->getUser())->getDisplayName() }}
            </td>
            <td style="text-align: center; vertical-align: top;">
                {{ $historyChange->getPrimaryId() }}
            </td>
            <td style="text-align: center; vertical-align: top;">
                {{ $historyChange->getReferenceId() }}
            </td>
            @php
                $decorator = HistoryChangesLocator::HistoryChangesDecorator($historyChange);
            @endphp
            <td class="description-cell">
                @switch($decorator->getEvent())
                    @case(Event::CREATE)
                        <div class="alert alert-success">
                            {{ $decorator->getActionEventText() }}
                        </div>
                        @break
                    @case(Event::UPDATE)
                        <div class="alert alert-warning">
                            {{ $decorator->getActionEventText() }}
                        </div>
                        @break
                    @case(Event::DELETE)
                        <div class="alert alert-danger">
                            {{ $decorator->getActionEventText() }}
                        </div>
                        @break
                @endswitch
                @if($decorator->getText())
                    <div class="alert">
                        {!! nl2br($decorator->getText()) !!}
                    </div>
                @endif
                @if ($decorator->getChanges())
                    <table style="width: 100%">
                        <thead>
                        <tr>
                            <th>Свойство</th>
                            <th>Было</th>
                            <th>Стало</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($decorator->getChanges() as $change)
                            <tr>
                                <td>{{ $change->getTitle() }}</td>
                                <td>{{ $change->getOldValue() }}</td>
                                <td>{{ $change->getNewValue() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </td>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
    @endforeach
    </tbody>
    <tfooter>
    </tfooter>
</table>
</body>
</html>
