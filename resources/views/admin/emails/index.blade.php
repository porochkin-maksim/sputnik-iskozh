<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::TITLE, 'История писем')

@section(SectionNames::CONTENT)
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">История отправленных писем</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email получателя</label>
                                        <input type="text"
                                               name="email"
                                               id="email"
                                               class="form-control"
                                               value="{{ request('email') }}"
                                               placeholder="Введите email">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Статус</label>
                                        <select name="status"
                                                id="status"
                                                class="form-select">
                                            <option value="">Все</option>
                                            @foreach($statuses as $status)
                                                <option value="{{ $status }}" @selected(request('status') == $status)>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sent_from" class="form-label">Отправлено с</label>
                                        <input type="date"
                                               name="sent_from"
                                               id="sent_from"
                                               class="form-control"
                                               value="{{ request('sent_from') }}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sent_to" class="form-label">Отправлено по</label>
                                        <input type="date"
                                               name="sent_to"
                                               id="sent_to"
                                               class="form-control"
                                               value="{{ request('sent_to') }}">
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fa fa-filter"></i> Фильтр
                                        </button>
                                        <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-undo"></i> Сбросить
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Получатель</th>
                                    <th>Тема</th>
                                    <th>Статус</th>
                                    <th>Отправлено</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($emails as $email)
                                    <tr>
                                        <td>{{ $email->id }}</td>
                                        <td>{{ $email->recipient_email }}<br><small>{{ $email->recipient_name }}</small>
                                        </td>
                                        <td>{{ Str::limit($email->subject, 50) }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($email->status) {
                                                    'delivered' => 'success',
                                                    'opened' => 'info',
                                                    'clicked' => 'primary',
                                                    'failed' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">{{ $email->status }}</span>
                                        </td>
                                        <td>{{ $email->sent_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.emails.show', $email) }}"
                                               class="btn btn-sm btn-info">Просмотр</a>
                                            <form action="{{ route('admin.emails.destroy', $email) }}" method="POST"
                                                  class="d-inline" onsubmit="return confirm('Удалить письмо?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Нет записей</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $emails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection