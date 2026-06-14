<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
?>
<nav class="side-menu">
    <a href="{{ route(RouteNames::CONTACTS) }}"
       class="@if(Route::is(RouteNames::CONTACTS)) active-link @endif">
        <i class="fa fa-phone"></i> <span>{{ RouteNames::name(RouteNames::CONTACTS) }}</span>
    </a>
    <a href="{{ route(RouteNames::REQUESTS) }}"
       class="@if(Route::is(RouteNames::REQUESTS)) active-link @endif">
        <i class="fa fa-lightbulb-o"></i> <span>{{ RouteNames::name(RouteNames::REQUESTS) }}</span>
    </a>
    <a href="{{ route(RouteNames::ANNOUNCEMENTS) }}"
       class="@if(Route::is(RouteNames::ANNOUNCEMENTS) || Route::is(RouteNames::ANNOUNCEMENTS_SHOW)) active-link @endif">
        <i class="fa fa-warning"></i> <span>{{ RouteNames::name(RouteNames::ANNOUNCEMENTS) }}</span>
    </a>
    <a href="{{ route(RouteNames::NEWS) }}"
       class="@if(Route::is(RouteNames::NEWS) || Route::is(RouteNames::NEWS_SHOW)) active-link @endif">
        <i class="fa fa-rss"></i> <span>{{ RouteNames::name(RouteNames::NEWS) }}</span>
    </a>
    <hr class="my-2">
    <a href="{{ route(RouteNames::GARBAGE) }}"
       class="@if(Route::is(RouteNames::GARBAGE)) active-link @endif">
        <i class="fa fa-trash"></i> <span>{{ RouteNames::name(RouteNames::GARBAGE) }}</span>
    </a>
    <a href="{{ route(RouteNames::FILES) }}"
       class="@if(Route::is(RouteNames::FILES)) active-link @endif">
        <i class="fa fa-file-o"></i> <span>{{ RouteNames::name(RouteNames::FILES) }}</span>
    </a>
    <a href="{{ route(RouteNames::REGULATION) }}"
       class="@if(Route::is(RouteNames::REGULATION)) active-link @endif">
        <i class="fa fa-book"></i> <span>{{ RouteNames::name(RouteNames::REGULATION) }}</span>
    </a>
</nav>
