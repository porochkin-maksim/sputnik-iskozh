<?php declare(strict_types=1);

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 */

use Core\Objects\Account\AccountLocator;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Auth;

$account = AccountLocator::AccountService()->getByUserId(Auth::id());

$routes = $account ? [
    RouteNames::COUNTERS,
    RouteNames::BILLING,
] : [];
?>

@foreach($routes as $r)
    <li class="nav-item">
        <a class="nav-link" href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
    </li>
@endforeach
