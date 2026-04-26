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
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Детали ошибки из файла: {{ $filename }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.error-logs.show', $filename) }}"
                               class="btn btn-default btn-sm text-nowrap">
                                <i class="fa fa-arrow-left"></i> Назад к списку ошибок
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
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
                                    if (isset($context->trace)){
                                        unset($context->trace);
                                    }
                                    $context = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                @endphp
                                <pre><code>{{ $context }}</code></pre>
                            </div>
                            @if(isset($entry->context->trace))
                                <div class="col-12">
                                    <h4>Трассировка</h4>
                                    <pre><code>{{ $entry->context->trace }}</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        pre {
            background-color : #f8f9fa;
            padding          : 1rem;
            border-radius    : 0.25rem;
            overflow-y       : auto;
        }
    </style>
@endpush 