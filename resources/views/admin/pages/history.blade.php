<?php declare(strict_types=1);

use Core\Domains\Infra\HistoryChanges\Collections\HistoryChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\User\UserLocator;
use Core\Enums\DateTimeFormat;
use Core\Requests\RequestArgumentsEnum;
use Core\Resources\RouteNames;

/**
 * @var HistoryChangesCollection $historyChanges
 * @var int                      $limit
 * @var int                      $offset
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>История изменений</title>
    @vite(['resources/sass/app.scss'])
    <style>
        body {
            font-size : 14px;
            padding   : 1rem;
        }

        .table-responsive {
            margin-bottom : 1rem;
        }

        .badge {
            font-size : 12px;
        }

        .changes-table {
            width            : 100%;
            margin-top       : 0.5rem;
            font-size        : 13px;
            background-color : white;
        }

        .changes-table th {
            background-color : #f8f9fa;
            font-weight      : 600;
        }

        .changes-table td, .changes-table th {
            padding : 0.5rem;
            border  : 1px solid #dee2e6;
        }

        .table > :not(caption) > * > * {
            padding : 0.5rem;
            border  : 1px solid #dee2e6;
        }

        .table-sm > :not(caption) > * > * {
            padding : 0.25rem;
        }

        .table > tbody > tr > td {
            vertical-align : top;
        }

        .controls-row {
            display         : flex;
            justify-content : space-between;
            align-items     : center;
            margin-bottom   : 1rem;
        }

        .table {
            border-collapse : collapse;
        }

        .table th {
            background-color : #f8f9fa;
            font-weight      : 600;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h4>История изменений</h4>
        </div>
    </div>

    <div class="card border-0 px-0">
        <div class="card-body px-0">
            <div class="controls-row">
                <div class="btn-group">
                    <select class="form-select form-select-sm"
                            style="width: auto;"
                            id="limitSelect">
                        <option value="25" {{ $limit === 25 ? 'selected' : '' }}>25 записей</option>
                        <option value="50" {{ $limit === 50 ? 'selected' : '' }}>50 записей</option>
                        <option value="100" {{ $limit === 100 ? 'selected' : '' }}>100 записей</option>
                    </select>
                </div>

                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        @php
                            $currentPage = floor($offset / $limit) + 1;
                            $startOffset = max(0, $offset - (5 * $limit));
                            $endOffset = count($historyChanges) >= $limit ? $offset + $limit : $offset;
                        @endphp

                        {{-- Previous page --}}
                        <li class="page-item {{ $offset === 0 ? 'disabled' : '' }}">
                            <a class="page-link"
                               href="{{ route(RouteNames::HISTORY_CHANGES, array_merge(request()->query(), ['skip' => max(0, $offset - $limit)])) }}"
                               aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        {{-- First page --}}
                        @if($offset > 5 * $limit)
                            <li class="page-item">
                                <a class="page-link"
                                   href="{{ route(RouteNames::HISTORY_CHANGES, array_merge(request()->query(), ['skip' => 0])) }}">1</a>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif

                        {{-- Page numbers --}}
                        @for($i = $startOffset; $i <= $endOffset; $i += $limit)
                            <li class="page-item {{ $i === $offset ? 'active' : '' }}">
                                <a class="page-link"
                                   href="{{ route(RouteNames::HISTORY_CHANGES, array_merge(request()->query(), ['skip' => $i])) }}">{{ floor($i / $limit) + 1 }}</a>
                            </li>
                        @endfor

                        {{-- Next page --}}
                        <li class="page-item {{ count($historyChanges) < $limit ? 'disabled' : '' }}">
                            <a class="page-link"
                               href="{{ route(RouteNames::HISTORY_CHANGES, array_merge(request()->query(), ['skip' => $offset + $limit])) }}"
                               aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                    <tr class="text-center">
                        <th class="text-center">ID</th>
                        <th class="text-center">Тип</th>
                        <th class="text-center">Дата</th>
                        <th class="text-center">Время</th>
                        <th class="text-center">Пользователь</th>
                        <th class="text-center">ID1</th>
                        <th>ID2</th>
                        <th>История</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($historyChanges as $historyChange)
                        @php
                            $decorator = HistoryChangesLocator::HistoryChangesDecorator($historyChange);
                            $user = UserLocator::UserDecorator($historyChange->getUser());
                            $createdAt = $historyChange->getCreatedAt();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $historyChange->getId() }}</td>
                            <td class="text-center">{{ $historyChange->getType()->name() }}</td>
                            <td class="text-center">{{ $createdAt->format(DateTimeFormat::DATE_VIEW_FORMAT) }}</td>
                            <td class="text-center">{{ $createdAt->format(DateTimeFormat::TIME_FULL) }}</td>
                            <td class="text-center text-nowrap">
                                @if ($user->getAdminViewUrl())
                                    <a href="{{ $user->getAdminViewUrl() }}" target="_blank">
                                        {{ $user->getDisplayName() }}
                                    </a>
                                @else
                                    {{ $user->getDisplayName() }}
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <a href="{{ $decorator->getPrimaryUrl() }}" style="color:white;" target="_blank">
                                        {{ $historyChange->getPrimaryId() }}
                                    </a>
                                </span>
                            </td>
                            <td>
                                @if($historyChange->getReferenceId())
                                    @php
                                        $badgeColor = match ($decorator->getEvent()) {
                                            Event::CREATE => 'success',
                                            Event::UPDATE => 'primary',
                                            Event::DELETE => 'danger',
                                            default => 'info',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }}">
                                        {{ $historyChange->getReferenceId() }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div>{!! nl2br($decorator->getActionEventText()) !!}</div>
                                @if($decorator->getText() && $decorator->getActionEventText() !== $decorator->getText())
                                    <div>{!! nl2br($decorator->getText()) !!}</div>
                                @endif
                                @if ($decorator->getChanges())
                                    <table class="changes-table">
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
                                                <td>{!! nl2br($change->getOldValue()) !!}</td>
                                                <td>{!! nl2br($change->getNewValue()) !!}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            const limitSelect = document.getElementById('limitSelect');

            limitSelect.addEventListener('change', function () {
                const searchParams = new URLSearchParams(window.location.search);
                searchParams.set('{{ RequestArgumentsEnum::LIMIT }}', this.value);
                searchParams.set('{{ RequestArgumentsEnum::SKIP }}', '0');
                window.location.href = window.location.pathname + '?' + searchParams.toString();
            });
        });
    </script>
</body>
</html>
