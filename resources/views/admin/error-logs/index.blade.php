<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::TITLE 'Логи ошибок')

@section(SectionNames::CONTENT)
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Файлы логов ошибок</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Имя файла</th>
                                        <th>Размер</th>
                                        <th>Дата изменения</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $log['name'] }}</td>
                                            <td>{{ number_format($log['size'] / 1024, 2) }} КБ</td>
                                            <td>{{ date('Y-m-d H:i:s', $log['modified']) }}</td>
                                            <td>
                                                <a href="{{ route('admin.error-logs.show', $log['name']) }}" 
                                                   class="btn btn-primary btn-sm text-nowrap">
                                                    <i class="fa fa-eye"></i> Просмотр
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Файлы логов не найдены</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 