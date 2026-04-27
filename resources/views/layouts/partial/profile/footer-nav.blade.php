<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */

if ( ! lc::account()->getId()) {
    return;
}
?>

<div class="bottom-nav">
    <div class="nav-item">
        <a class="nav-link @if(Route::is(RouteNames::PROFILE_COUNTERS)) active @endif"
           href="{{ route(RouteNames::PROFILE_COUNTERS) }}">
            <i class="fa fa-bolt"></i> <span>{{ RouteNames::name(RouteNames::PROFILE_COUNTERS) }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a class="nav-link @if(Route::is(RouteNames::PROFILE_INVOICES)) active @endif"
           href="{{ route(RouteNames::PROFILE_INVOICES) }}">
            <i class="fa fa-money"></i> <span>{{ RouteNames::name(RouteNames::PROFILE_INVOICES) }}</span>
        </a>
    </div>
    <div class="nav-item">
        <a class="nav-link"
           href="{{ route(RouteNames::HOME) }}">
            <i class="fa fa-user"></i> <span>Профиль</span>
        </a>
    </div>
</div>