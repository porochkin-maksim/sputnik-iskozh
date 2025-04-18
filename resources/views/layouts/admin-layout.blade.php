<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Session\CookieNames;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;

$bgImage = StaticFileLocator::StaticFileService()->seasonBgImage();

$season = match (Carbon::now()->month) {
    3, 4, 5   => 'spring',
    6, 7, 8   => 'summer',
    9, 10, 11 => 'autumn',
    default   => 'winter',
};

$authRole  = \lc::roleDecorator();

$cutRouteNameFn = static function (string $routeName) {
    $parts = explode('.', $routeName);
    array_splice($parts, 2, count($parts) + 1);

    return implode('.', $parts) . '.*';
};
?>
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield(SectionNames::TITLE, RouteNames::name(Route::current()?->getName(), env('APP_NAME')))</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack(SectionNames::META)
    @stack(SectionNames::STYLES)
    @stack(SectionNames::SCRIPTS)
</head>
<body class="d-flex flex-column h-100 {{ $season }} admin"
      id="app"
      style="background-image: url('{{ $bgImage->getUrl() }}')">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom"
     id="topNavBar">
    <div class="container-fluid main px-3">
        <a class="navbar-brand"
           href="{{ url('/') }}">
            <div class="logo"
                 style="background-image: url('{{ StaticFileLocator::StaticFileService()->logoSnt()->getUrl() }}')"></div>
            {{ env('APP_NAME') }}
        </a>
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#topMenuNavContent"
                aria-controls="topMenuNavContent"
                aria-expanded="false"
                aria-label="Переключатель навигации">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse"
             id="topMenuNavContent">
            @include('layouts.partial.nav')
        </div>
    </div>
</nav>
<main class="px-3 py-2 w-100">
    <div class="row admin-content-body">
        <div class="col-2 admin-side-panel border-end">
            <div class="side-menu">
                <a class="@if(Route::is(RouteNames::ADMIN)) active-link @endif" href="{{ route(RouteNames::ADMIN) }}">
                    <i class="fa fa-home me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN) }}</span>
                </a>

                @if($authRole->can(PermissionEnum::ROLES_VIEW))
                <a class="@if(Route::is('admin.role.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_ROLE_INDEX) }}">
                    <i class="fa fa-users me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_ROLE_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::USERS_VIEW))
                <a class="@if(Route::is('admin.user.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_USER_INDEX) }}">
                    <i class="fa fa-user me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_USER_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::ACCOUNTS_VIEW))
                <a class="@if(Route::is('admin.account.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_ACCOUNT_INDEX) }}">
                    <i class="fa fa-building me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_ACCOUNT_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::PERIODS_VIEW))
                <a class="@if(Route::is('admin.period.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_PERIOD_INDEX) }}">
                    <i class="fa fa-calendar me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_PERIOD_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::SERVICES_VIEW))
                <a class="@if(Route::is('admin.service.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_SERVICE_INDEX) }}">
                    <i class="fa fa-cogs me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_SERVICE_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::INVOICES_VIEW))
                <a class="@if(Route::is('admin.invoice.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_INVOICE_INDEX) }}">
                    <i class="fa fa-file-text me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_INVOICE_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::COUNTERS_VIEW))
                    <a class="@if(Route::is('admin.counter-history.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_COUNTER_HISTORY_INDEX) }}">
                        <i class="fa fa-tachometer me-2"></i>
                        <span>{{ RouteNames::name(RouteNames::ADMIN_COUNTER_HISTORY_INDEX) }}</span>
                    </a>
                @endif

                @if($authRole->can(PermissionEnum::PAYMENTS_VIEW))
                <a class="@if(Route::is('admin.new-payment.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_NEW_PAYMENT_INDEX) }}">
                    <i class="fa fa-credit-card me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_NEW_PAYMENT_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->can(PermissionEnum::OPTIONS_VIEW))
                <a class="@if(Route::is('admin.options.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_OPTIONS_INDEX) }}">
                    <i class="fa fa-sliders me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_OPTIONS_INDEX) }}</span>
                </a>
                @endif

                @if($authRole->canAccessAdmin())
                <a class="@if(Route::is('admin.error-logs.*')) active-link @endif" href="{{ route(RouteNames::ADMIN_ERRORS) }}">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    <span>{{ RouteNames::name(RouteNames::ADMIN_ERRORS) }}</span>
                </a>
                @endif
            </div>
        </div>
        <div class="col-10">
            @yield(SectionNames::CONTENT)
        </div>
    </div>
</main>
<footer class="w-100">
    <div class="d-flex justify-content-center py-3">

    </div>
</footer>
<alerts-block></alerts-block>
</body>
</html>
