<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */

$routes = [
    RouteNames::CONTACTS,
    RouteNames::ANNOUNCEMENTS,
    RouteNames::NEWS,
    RouteNames::GARBAGE,
    RouteNames::FILES,
    RouteNames::RUBRICS,
    RouteNames::REGULATION,
];
?>

<div class="vertical-menu vertical-menu-light d-block d-lg-none">
    <ul>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::CONTACTS)) active @endif"
               href="{{ route(RouteNames::CONTACTS) }}">
                <i class="fa fa-phone"></i>&nbsp;{{ RouteNames::name(RouteNames::CONTACTS) }}
            </a>
            <ul class="vertical-menu">
                <li class="nav-item">
                    <a class="nav-link @if(Route::is(RouteNames::REQUESTS)) active @endif"
                       href="{{ route(RouteNames::REQUESTS) }}">
                        <i class="fa fa-lightbulb-o"></i>&nbsp;{{ RouteNames::name(RouteNames::REQUESTS) }}
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::ANNOUNCEMENTS) || Route::is(RouteNames::ANNOUNCEMENTS_SHOW)) active @endif"
               href="{{ route(RouteNames::ANNOUNCEMENTS) }}">
                <i class="fa fa-warning"></i>&nbsp;{{ RouteNames::name(RouteNames::ANNOUNCEMENTS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::NEWS) || Route::is(RouteNames::NEWS_SHOW)) active @endif"
               href="{{ route(RouteNames::NEWS) }}">
                <i class="fa fa-rss"></i>&nbsp;{{ RouteNames::name(RouteNames::NEWS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::GARBAGE)) active @endif"
               href="{{ route(RouteNames::GARBAGE) }}">
                <i class="fa fa-trash"></i>&nbsp;{{ RouteNames::name(RouteNames::GARBAGE) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::FILES)) active @endif"
               href="{{ route(RouteNames::FILES) }}">
                <i class="fa fa-file-o"></i>&nbsp;{{ RouteNames::name(RouteNames::FILES) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::RUBRICS)) active @endif"
               href="{{ route(RouteNames::RUBRICS) }}">
                <i class="fa fa-cubes"></i>&nbsp;{{ RouteNames::name(RouteNames::RUBRICS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if(Route::is(RouteNames::REGULATION)) active @endif"
               href="{{ route(RouteNames::REGULATION) }}">
                <i class="fa fa-book"></i>&nbsp;{{ RouteNames::name(RouteNames::REGULATION) }}
            </a>
        </li>
        @guest
            <li class="nav-item">
                <auth-block></auth-block>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::PROFILE) }}">
                    {{ \lc::userDecorator()->getDisplayName() }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::LOGOUT) }}">
                    <span class="d-lg-none">{{ RouteNames::name(RouteNames::LOGOUT) }}</span>
                    &nbsp;<i class="fa fa-sign-out"></i>
                </a>
            </li>
        @endguest
    </ul>
</div>
<div class="horizontal-menu d-none d-lg-flex justify-content-between w-100">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle"
               data-bs-toggle="dropdown"
               href="#"
               role="button"
               aria-expanded="false">{{ RouteNames::name(RouteNames::CONTACTS) }}</a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item"
                       href="{{ route(RouteNames::CONTACTS) }}">
                        {{ RouteNames::name(RouteNames::CONTACTS) }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                       href="{{ route(RouteNames::REQUESTS) }}">
                        {{ RouteNames::name(RouteNames::REQUESTS) }}
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::ANNOUNCEMENTS) }}">
                {{ RouteNames::name(RouteNames::ANNOUNCEMENTS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::NEWS) }}">
                {{ RouteNames::name(RouteNames::NEWS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::GARBAGE) }}">
                {{ RouteNames::name(RouteNames::GARBAGE) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::FILES) }}">
                {{ RouteNames::name(RouteNames::FILES) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::RUBRICS) }}">
                {{ RouteNames::name(RouteNames::RUBRICS) }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route(RouteNames::REGULATION) }}">
                {{ RouteNames::name(RouteNames::REGULATION) }}
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ms-auto">
        @guest
            <li class="nav-item">
                <auth-block></auth-block>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::PROFILE) }}">
                    {{ \lc::userDecorator()->getDisplayName() }} {!! \lc::account() ? sprintf('(<i class="fa fa-home"></i>&nbsp;%s)', \lc::account()->getNumber()) : '' !!}
                </a>
            </li>
            @if (lc::roleDecorator()->canAccessAdmin())
                <li class="nav-item">
                    <a class="nav-link"
                       href="{{ route(RouteNames::ADMIN) }}">
                        <i class="fa fa-gears"></i>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::LOGOUT) }}">
                    <span class="d-lg-none">{{ RouteNames::name(RouteNames::LOGOUT) }}</span>
                    <i class="fa fa-sign-out"></i>
                </a>
            </li>
        @endguest
    </ul>
</div>