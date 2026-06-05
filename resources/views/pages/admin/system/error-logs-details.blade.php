<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
use Carbon\Carbon;
use Core\Shared\Helpers\DateTime\DateTimeFormat;

?>

@extends('layouts.admin-layout')

@section(SectionNames::TITLE, 'Детали ошибки')

@section(SectionNames::CONTENT)
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-admin.card>
                    <x-slot:header>
                        <x-admin.card-header>
                            <x-slot:title>
                                <h3 class="card-title m-0">Детали ошибки из файла: {{ $filename }}</h3>
                            </x-slot:title>
                            <x-slot:tools>
                                <a href="{{ route('admin.error-logs.show', $filename) }}"
                                   class="btn btn-default btn-sm text-nowrap">
                                    <i class="fa fa-arrow-left"></i> Назад к списку ошибок
                                </a>
                            </x-slot:tools>
                        </x-admin.card-header>
                    </x-slot:header>

                    <div class="row">
                        <div class="col-12">
                            <h4>Основная информация</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Дата и время</th>
                                    <td class="text-nowrap">{{ Carbon::parse($entry->datetime)->format(DateTimeFormat::DATE_TIME_DEFAULT) }}</td>
                                </tr>
                                <tr>
                                    <th>Уровень</th>
                                    <td>
                                        <span class="fw-bold text-{{
                                            match($entry->level_name) {
                                                'ERROR' => 'danger',
                                                'WARNING' => 'warning',
                                                'INFO' => 'info',
                                                default => 'secondary'
                                            }
                                        }}">
                                            {{ $entry->level_name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Сообщение</th>
                                    <td>{{ $entry->message }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <h4>Контекст</h4>
                            @php
                                $context = clone $entry->context;
                                if (isset($context->trace)) {
                                    unset($context->trace);
                                }
                                if (isset($context->previous)) {
                                    unset($context->previous);
                                }
                                $context = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            @endphp
                            <x-shared.preformatted class="bg-light p-4 rounded overflow-y-auto">{{ $context }}</x-shared.preformatted>
                        </div>
                        @if(isset($entry->context->trace))
                            <div class="col-12">
                                <h4>Трассировка</h4>
                                @php
                                    $trace = $entry->context->trace;
                                    if (is_array($trace) || is_object($trace)) {
                                        $trace = json_encode($trace, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                @endphp
                                <x-shared.preformatted class="bg-light p-4 rounded overflow-y-auto">{{ $trace }}</x-shared.preformatted>
                            </div>
                        @endif
                        @if(isset($entry->context->previous) && $entry->context->previous)
                            <div class="col-12">
                                <h4>Предыдущее исключение</h4>
                                @php
                                    $previous = $entry->context->previous;
                                    if (is_array($previous) || is_object($previous)) {
                                        $previous = json_encode($previous, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                    }
                                @endphp
                                <x-shared.preformatted class="bg-light p-4 rounded overflow-y-auto">{{ $previous }}</x-shared.preformatted>
                            </div>
                        @endif
                    </div>
                </x-admin.card>
            </div>
        </div>
    </div>
@endsection
