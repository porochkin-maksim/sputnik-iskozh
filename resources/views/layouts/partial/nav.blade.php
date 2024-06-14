<?php declare(strict_types=1);

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */

use Core\Objects\User\UserLocator;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Auth;

$user          = Auth::user();
$userDecorator = UserLocator::UserDecorator($user);

$routes = [
    RouteNames::FILES,
    RouteNames::NEWS,
];
?>

<div class="vertical-menu vertical-menu-light d-block d-lg-none">
    <ul>
        @foreach($routes as $r)
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
            </li>
        @endforeach
        @guest
            <li class="nav-item">
                <auth-block></auth-block>
            </li>
        @else
            @relativeInclude('account-nav')
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route(RouteNames::PROFILE) }}">
                    {{ $userDecorator->getDisplayName() }}
                </a>
            </li>
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
<div class="d-none d-lg-flex justify-content-between w-100">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @foreach($routes as $r)
            <li class="nav-item">
                <a class="nav-link"
                   href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
            </li>
        @endforeach
        <li class="nav-item">
            <a class="nav-link"
               href="{{ Storage::url('Устав.pdf') }}"
               target="_blank">Устав</a>
        </li>
    </ul>

    <ul class="navbar-nav ms-auto">
        @guest
            <li class="nav-item">
                <auth-block></auth-block>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" href="{{ route(RouteNames::PROFILE) }}">
                   {{ $userDecorator->getDisplayName() }}
                </a>
            </li>
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