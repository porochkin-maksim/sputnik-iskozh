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
$navRoutes = [RouteNames::ADMIN];

$cutRouteNameFn = static function (string $routeName) {
    $parts = explode('.', $routeName);
    array_splice($parts, 2, count($parts) + 1);

    return implode('.', $parts) . '.*';
};

if ($authRole->can(PermissionEnum::ROLES_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_ROLE_INDEX;
}
if ($authRole->can(PermissionEnum::USERS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_USER_INDEX;
}
if ($authRole->can(PermissionEnum::OPTIONS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_OPTIONS_INDEX;
}
if ($authRole->can(PermissionEnum::ACCOUNTS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_ACCOUNT_INDEX;
}
if ($authRole->can(PermissionEnum::PERIODS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_PERIOD_INDEX;
}
if ($authRole->can(PermissionEnum::SERVICES_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_SERVICE_INDEX;
}
if ($authRole->can(PermissionEnum::INVOICES_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_INVOICE_INDEX;
}
if ($authRole->can(PermissionEnum::PAYMENTS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_NEW_PAYMENT_INDEX;
}
if ($authRole->can(PermissionEnum::COUNTERS_VIEW)) {
    $navRoutes[] = RouteNames::ADMIN_COUNTER_HISTORY_INDEX;
}
$navRoutes[] = RouteNames::ADMIN_ERRORS;
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
                @foreach($navRoutes as $routeCode)
                    <a class="@if(Route::is($cutRouteNameFn($routeCode))) active-link @endif"
                       href="{{ route($routeCode) }}">
                        {{ RouteNames::name($routeCode) }}
                    </a>
                @endforeach
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
