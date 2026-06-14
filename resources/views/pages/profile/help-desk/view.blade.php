<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

/**
 * @var \Core\Domains\HelpDesk\Models\TicketEntity $ticket
 * @var \Core\Domains\HelpDesk\Models\TicketCommentEntity[] $comments
 */

$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_HELP_DESK_VIEW, $ticket);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, 'Заявка №' . $ticket->getId())

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_HELP_DESK_VIEW, $ticket) }}

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <i class="{{ $ticket->getType()?->icon() }} fa-2x"></i>
                <div>
                    <h5 class="mb-1">Заявка №{{ $ticket->getId() }}</h5>
                    <div class="text-muted small">
                        {{ $ticket->getType()?->name() }}
                        · {{ $ticket->getCategory()?->getName() }}
                        @if($ticket->getService())
                            · {{ $ticket->getService()->getName() }}
                        @endif
                    </div>
                </div>
                <span class="ms-auto badge badge-{{ match($ticket->getStatus()?->value) { 1 => 'warning', 2 => 'info', 3 => 'primary', 4 => 'secondary', 5 => 'danger', default => 'secondary' } }}" style="font-size:0.9rem">
                    {{ $ticket->getStatus()?->name() }}
                </span>
            </div>

            @if($ticket->getDescription())
                <div class="mb-3">
                    <strong>Описание:</strong>
                    <p class="mb-0 mt-1" style="white-space:pre-wrap">{{ $ticket->getDescription() }}</p>
                </div>
            @endif

            @if($ticket->getResult())
                <div class="mb-3 p-3 bg-light rounded">
                    <strong>Ответ:</strong>
                    <p class="mb-0 mt-1" style="white-space:pre-wrap">{{ $ticket->getResult() }}</p>
                </div>
            @endif

            <div class="d-flex gap-4 text-muted small">
                <span>Создана: {{ $ticket->getCreatedAt()?->format('d.m.Y H:i') }}</span>
                @if($ticket->getResolvedAt())
                    <span>Закрыта: {{ $ticket->getResolvedAt()->format('d.m.Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    @if(count($comments))
        <div class="card">
            <div class="card-body p-4">
                <h6 class="border-bottom pb-2 mb-3">История ответов</h6>
                @foreach($comments as $comment)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>{{ $comment->getUser()?->getFullName() ?: 'Администратор' }}</span>
                            <span>{{ $comment->getCreatedAt()?->format('d.m.Y H:i') }}</span>
                        </div>
                        <p class="mb-0" style="white-space:pre-wrap">{{ $comment->getComment() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-3">
        <a href="{{ route(RouteNames::PROFILE_HELP_DESK_INDEX) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i>Назад к списку
        </a>
    </div>
@endsection
