<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

$hasAccount = (bool) lc::account()->getId();
?>
<nav class="side-menu">
    @if ($hasAccount)
        <a href="{{ route(RouteNames::PROFILE_COUNTERS) }}"
           class="@if(Route::is(RouteNames::PROFILE_COUNTERS)) active-link @endif">
            <i class="fa fa-bolt"></i> <span>{{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}</span>
        </a>
        <a href="{{ route(RouteNames::PROFILE_INVOICES) }}"
           class="@if(Route::is(RouteNames::PROFILE_INVOICES)) active-link @endif">
            <i class="fa fa-money"></i> <span>{{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}</span>
        </a>
        <a href="{{ route(RouteNames::PROFILE_PAYMENTS_INDEX) }}"
           class="@if(Route::is(RouteNames::PROFILE_PAYMENTS_INDEX)) active-link @endif">
            <i class="fa fa-credit-card"></i> <span>Платежи</span>
        </a>
        <a href="{{ route(RouteNames::PROFILE_HELP_DESK_INDEX) }}"
           class="@if(Route::is(RouteNames::PROFILE_HELP_DESK_INDEX)) active-link @endif">
            <i class="fa fa-bullhorn"></i> <span>Заявки</span>
        </a>
        <hr class="my-2">
        <a href="{{ route(RouteNames::INDEX) }}">
            <i class="fa fa-mail-reply"></i> <span>На главную сайта</span>
        </a>
    @endif
    <a href="{{ route(RouteNames::LOGOUT) }}">
        <i class="fa fa-sign-out"></i> <span>{{ RouteNames::name(RouteNames::LOGOUT) }}</span>
    </a>
</nav>
