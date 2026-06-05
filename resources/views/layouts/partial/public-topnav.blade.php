<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
?>

<div class="horizontal-menu d-flex flex-column flex-lg-row justify-content-between w-100 align-items-start align-items-lg-center">
    <ul class="navbar-nav me-lg-auto mb-0 site-menu__nav">
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::CONTACTS)) active @endif"
               href="{{ route(RouteNames::CONTACTS) }}">
                <i class="fa fa-phone"></i>
                <span>{{ RouteNames::name(RouteNames::CONTACTS) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::REQUESTS)) active @endif"
               href="{{ route(RouteNames::REQUESTS) }}">
                <i class="fa fa-lightbulb-o"></i>
                <span>{{ RouteNames::name(RouteNames::REQUESTS) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::ANNOUNCEMENTS) || Route::is(RouteNames::ANNOUNCEMENTS_SHOW)) active @endif"
               href="{{ route(RouteNames::ANNOUNCEMENTS) }}">
                <i class="fa fa-warning"></i>
                <span>{{ RouteNames::name(RouteNames::ANNOUNCEMENTS) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::NEWS) || Route::is(RouteNames::NEWS_SHOW)) active @endif"
               href="{{ route(RouteNames::NEWS) }}">
                <i class="fa fa-rss"></i>
                <span>{{ RouteNames::name(RouteNames::NEWS) }}</span>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav d-lg-none mb-0 mt-1 site-menu__nav">
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::GARBAGE)) active @endif"
               href="{{ route(RouteNames::GARBAGE) }}">
                <i class="fa fa-trash"></i>
                <span>{{ RouteNames::name(RouteNames::GARBAGE) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::FILES)) active @endif"
               href="{{ route(RouteNames::FILES) }}">
                <i class="fa fa-file-o"></i>
                <span>{{ RouteNames::name(RouteNames::FILES) }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::REGULATION)) active @endif"
               href="{{ route(RouteNames::REGULATION) }}">
                <i class="fa fa-book"></i>
                <span>{{ RouteNames::name(RouteNames::REGULATION) }}</span>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ms-lg-auto mb-0 site-menu__nav">
        @guest
            <li class="nav-item">
                <auth-block :has-modal="true"></auth-block>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link site-menu__account"
                   href="{{ route(RouteNames::HOME) }}">
                    <span class="site-menu__account-name">{{ \lc::userDecorator()->getDisplayName() }}</span>
                    {!! \lc::account() ? sprintf('<span class="site-menu__account-chip"><i class="fa fa-home"></i>&nbsp;%s</span>', \lc::account()->getNumber()) : '' !!}
                </a>
            </li>
            @if (lc::roleDecorator()->canAccessAdmin() && !lc::isAndroid())
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
        @endguest
    </ul>
</div>
