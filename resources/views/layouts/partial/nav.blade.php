<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */
?>

<div class="site-menu site-menu--mobile d-block d-lg-none">
    <div class="site-menu__group">
        <div class="site-menu__title">Сайт</div>
        <a class="site-menu__link @if(Route::is(RouteNames::CONTACTS)) active @endif"
           href="{{ route(RouteNames::CONTACTS) }}">
            <i class="fa fa-phone"></i>
            <span>{{ RouteNames::name(RouteNames::CONTACTS) }}</span>
        </a>
        <a class="site-menu__link @if(Route::is(RouteNames::REQUESTS)) active @endif"
           href="{{ route(RouteNames::REQUESTS) }}">
            <i class="fa fa-lightbulb-o"></i>
            <span>{{ RouteNames::name(RouteNames::REQUESTS) }}</span>
        </a>
        <a class="site-menu__link @if(Route::is(RouteNames::ANNOUNCEMENTS) || Route::is(RouteNames::ANNOUNCEMENTS_SHOW)) active @endif"
           href="{{ route(RouteNames::ANNOUNCEMENTS) }}">
            <i class="fa fa-warning"></i>
            <span>{{ RouteNames::name(RouteNames::ANNOUNCEMENTS) }}</span>
        </a>
        <a class="site-menu__link @if(Route::is(RouteNames::NEWS) || Route::is(RouteNames::NEWS_SHOW)) active @endif"
           href="{{ route(RouteNames::NEWS) }}">
            <i class="fa fa-rss"></i>
            <span>{{ RouteNames::name(RouteNames::NEWS) }}</span>
        </a>
    </div>

    <div class="site-menu__group">
        <div class="site-menu__title">Документы</div>
        <a class="site-menu__link @if(Route::is(RouteNames::GARBAGE)) active @endif"
           href="{{ route(RouteNames::GARBAGE) }}">
            <i class="fa fa-trash"></i>
            <span>{{ RouteNames::name(RouteNames::GARBAGE) }}</span>
        </a>
        <a class="site-menu__link @if(Route::is(RouteNames::FILES)) active @endif"
           href="{{ route(RouteNames::FILES) }}">
            <i class="fa fa-file-o"></i>
            <span>{{ RouteNames::name(RouteNames::FILES) }}</span>
        </a>
        <a class="site-menu__link @if(Route::is(RouteNames::REGULATION)) active @endif"
           href="{{ route(RouteNames::REGULATION) }}">
            <i class="fa fa-book"></i>
            <span>{{ RouteNames::name(RouteNames::REGULATION) }}</span>
        </a>
    </div>

    <div class="site-menu__group">
        <div class="site-menu__title">Профиль</div>
        @guest
            <div class="site-menu__auth">
                <auth-block></auth-block>
            </div>
        @else
            <a class="site-menu__link"
               href="{{ route(RouteNames::HOME) }}">
                <i class="fa fa-home"></i>
                <span>{{ \lc::userDecorator()->getDisplayName() }}</span>
            </a>
            @if (lc::roleDecorator()->canAccessAdmin() && !lc::isAndroid())
                <a class="site-menu__link"
                   href="{{ route(RouteNames::ADMIN) }}">
                    <i class="fa fa-gears"></i>
                    <span>Админка</span>
                </a>
            @endif
            <a class="site-menu__link"
               href="{{ route(RouteNames::LOGOUT) }}">
                <i class="fa fa-sign-out"></i>
                <span>{{ RouteNames::name(RouteNames::LOGOUT) }}</span>
            </a>
        @endguest
    </div>
</div>
<div class="site-menu site-menu--desktop d-none d-lg-flex justify-content-between w-100 align-items-center">
    <div class="site-menu__desktop-grid">
        <ul class="navbar-nav mb-0 site-menu__nav site-menu__nav--left">
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

        <ul class="navbar-nav mb-0 site-menu__nav site-menu__nav--right">
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
    </div>

    <ul class="navbar-nav ms-auto mb-0 site-menu__nav">
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
