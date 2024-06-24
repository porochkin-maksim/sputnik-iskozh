<?php declare(strict_types=1);

use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\Services\UserDecorator;
use Core\Resources\RouteNames;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 * @var UserDecorator $userDecorator
 * @var AccountDTO    $account
 */

$routes = [
    RouteNames::CONTACTS,
    RouteNames::NEWS,
    RouteNames::FILES,
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
                <a class="nav-link"
                   href="{{ route(RouteNames::PROFILE) }}">
                    {{ $userDecorator->getDisplayName() }} {!! $account ? sprintf('(<i class="fa fa-home"></i>&nbsp;%s)',$account->getNumber()) : '' !!}
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