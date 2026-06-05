<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
use Carbon\Carbon;
use Core\Shared\Helpers\DateTime\DateTimeFormat;

?>

@extends('layouts.admin-layout')

@section(SectionNames::TITLE, 'Просмотр лога ошибок')

@section(SectionNames::CONTENT)
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-admin.card>
                    <x-slot:header>
                        <x-admin.card-header>
                            <x-slot:title>
                                <h3 class="card-title m-0">Просмотр лога: {{ $filename }}</h3>
                            </x-slot:title>
                            <x-slot:tools>
                                <a href="{{ route('admin.error-logs.index') }}"
                                   class="btn btn-default btn-sm text-nowrap">
                                    <i class="fa fa-arrow-left"></i> Назад к списку
                                </a>
                            </x-slot:tools>
                        </x-admin.card-header>
                    </x-slot:header>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Уровень</th>
                                <th>Сообщение</th>
                                <th>Контекст</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($entries as $index => $entry)
                                <tr>
                                    <td class="text-nowrap">{{ Carbon::parse($entry->datetime)->format(DateTimeFormat::DATE_TIME_DEFAULT) }}</td>
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
                                    <td>{{ $entry->message }}</td>
                                    <td>
                                        <a href="{{ route('admin.error-logs.details', ['filename' => $filename, 'index' => $index]) }}"
                                           class="btn btn-info btn-sm text-nowrap">
                                            <i class="fa fa-info-circle"></i> Подробности
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Записи не найдены</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-admin.card>
            </div>
        </div>
    </div>
@endsection
