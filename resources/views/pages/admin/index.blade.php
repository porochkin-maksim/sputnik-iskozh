<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.admin-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection



@section(SectionNames::CONTENT)

    {{-- Важные предупреждения --}}
    @if (isset($alerts['unlinkedPayments']) && $alerts['unlinkedPayments'] > 0)
        <div class="alert alert-danger d-flex align-items-center gap-3 py-3 mb-4">
            <i class="fa fa-exclamation-triangle fa-2x"></i>
            <div>
                <strong class="fs-5">{{ $alerts['unlinkedPayments'] }} платеж{{ $alerts['unlinkedPayments'] > 1 ? 'ей' : '(-а)' }} без привязки к счету</strong>
                <p class="mb-0 mt-1">Платежи из публичной формы и личного кабинета, которые ещё не привязаны к счету участка. <a href="{{ route(RouteNames::ADMIN_NEW_PAYMENT_INDEX) }}" class="alert-link">Перейти к обработке</a></p>
            </div>
        </div>
    @endif

    @if (isset($alerts['counterHistories']) && ($alerts['counterHistories']['unlinked'] > 0 || $alerts['counterHistories']['unverified'] > 0))
        <div class="alert alert-warning d-flex align-items-center gap-3 py-3 mb-4">
            <i class="fa fa-bolt fa-2x"></i>
            <div>
                @if ($alerts['counterHistories']['unlinked'] > 0)
                    <strong class="fs-5">{{ $alerts['counterHistories']['unlinked'] }} показани{{ $alerts['counterHistories']['unlinked'] > 1 ? 'й' : 'е' }} счётчиков без привязки</strong>
                    <p class="mb-0 mt-1">Показания электроэнергии, которые не привязаны к прибору учёта.</p>
                @endif
                @if ($alerts['counterHistories']['unverified'] > 0)
                    <strong class="fs-5">{{ $alerts['counterHistories']['unverified'] }} неподтверждённы{{ $alerts['counterHistories']['unverified'] > 1 ? 'х' : 'х' }} показани{{ $alerts['counterHistories']['unverified'] > 1 ? 'й' : 'е' }}</strong>
                    <p class="mb-0 mt-1">Показания, ожидающие подтверждения администратором.</p>
                @endif
                <a href="{{ route(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX) }}" class="alert-link">Перейти к обработке</a>
            </div>
        </div>
    @endif

    <dashboard-cards-block />
@endsection
