<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */

$hasAccount = (bool) lc::account()->getId();
?>

<div class="vertical-menu vertical-menu-light d-block d-lg-none">
    <ul>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::HOME) }}">
                <i class="fa fa-user"></i> {{ \lc::userDecorator()->getDisplayName() }}
            </a>
        </li>
        @if ($hasAccount)
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::PROFILE_COUNTERS)) active @endif"
               href="{{ route(RouteNames::PROFILE_COUNTERS) }}">
                <i class="fa fa-tachometer"></i> {{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::PROFILE_INVOICES)) active @endif"
               href="">
                <i class="fa fa-file-text"></i> {{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route(RouteNames::INDEX) }}">
                <i class="fa fa-mail-reply"></i> Вернуться На сайт
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::LOGOUT) }}">
                <i class="fa fa-sign-out"></i>&nbsp;{{ RouteNames::name(RouteNames::LOGOUT) }}
            </a>
        </li>
    </ul>
</div>
<div class="horizontal-menu d-none d-lg-flex justify-content-between w-100">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @if ($hasAccount)
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::PROFILE_COUNTERS) }}">
                <i class="fa fa-tachometer"></i>  {{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::PROFILE_INVOICES) }}">
                <i class="fa fa-file-text"></i>  {{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::INDEX) }}">
                <i class="fa fa-mail-reply"></i> Вернуться На сайт
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::HOME) }}">
                {{ \lc::userDecorator()->getDisplayName() }} {!! \lc::account() ? sprintf('(<i class="fa fa-home"></i>&nbsp;%s)', \lc::account()->getNumber()) : '' !!}
            </a>
        </li>
        @if (lc::roleDecorator()->canAccessAdmin())
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::ADMIN) }}">
                    <i class="fa fa-gears"></i> Админка
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::LOGOUT) }}">
                <i class="fa fa-sign-out"></i>&nbsp;{{ RouteNames::name(RouteNames::LOGOUT) }}
            </a>
        </li>
    </ul>
</div>