<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
?>

<div class="site-subnav__shell">
    <div class="site-subnav__inner">
        <div class="site-subnav__group site-subnav__group--simple">
            <div class="site-subnav__links">
                <a class="site-subnav__link @if(Route::is(RouteNames::GARBAGE)) active @endif"
                   href="{{ route(RouteNames::GARBAGE) }}">
                    <i class="fa fa-trash"></i>
                    <span>{{ RouteNames::name(RouteNames::GARBAGE) }}</span>
                </a>
                <a class="site-subnav__link @if(Route::is(RouteNames::FILES)) active @endif"
                   href="{{ route(RouteNames::FILES) }}">
                    <i class="fa fa-file-o"></i>
                    <span>{{ RouteNames::name(RouteNames::FILES) }}</span>
                </a>
                <a class="site-subnav__link @if(Route::is(RouteNames::REGULATION)) active @endif"
                   href="{{ route(RouteNames::REGULATION) }}">
                    <i class="fa fa-book"></i>
                    <span>{{ RouteNames::name(RouteNames::REGULATION) }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
