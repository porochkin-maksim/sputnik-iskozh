<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.admin-layout')

@section(SectionNames::TITLE, 'Письмо #' . $email->id)

@section(SectionNames::CONTENT)
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Детали письма #{{ $email->id }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.emails.index') }}" class="btn btn-secondary btn-sm"><i
                                        class="fa fa-reply"></i> Назад</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID</th>
                                <td>{{ $email->id }}</td>
                            </tr>
                            <tr>
                                <th>Message ID</th>
                                <td><code>{{ $email->message_id }}</code></td>
                            </tr>
                            <tr>
                                <th>Получатель</th>
                                <td>{{ $email->recipient_email }} @if($email->recipient_name)
                                        ({{ $email->recipient_name }})
                                    @endif</td>
                            </tr>
                            <tr>
                                <th>Тема</th>
                                <td>{{ $email->subject }}</td>
                            </tr>
                            <tr>
                                <th>Статус</th>
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
                            </tr>
                            <tr>
                                <th>Mailer</th>
                                <td>{{ $email->mailer }}</td>
                            </tr>
                            <tr>
                                <th>Отправлено</th>
                                <td>{{ $email->sent_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Доставлено</th>
                                <td>{{ $email->delivered_at ? $email->delivered_at->format('d.m.Y H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Вложения</th>
                                <td>
                                    @if($email->attachments)
                                        <ul>
                                            @foreach($email->attachments as $attachment)
                                                <li>{{ $attachment['filename'] }} ({{ $attachment['content_type'] }}, {{ $attachment['size'] }} байт)</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Метаданные</th>
                                <td>
                                    <pre>{{ json_encode($email->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                            </tr>
                        </table>

                        <div class="mt-4">
                            <h5>Содержимое письма</h5>
                            <div class="border p-3 bg-light">
                                {!! $email->body !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Действия</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.emails.destroy', $email) }}" method="POST"
                              onsubmit="return confirm('Удалить письмо?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">Удалить запись</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection