<?php declare(strict_types=1);

use Core\Domains\Account\Models\AccountDTO;
use Core\Resources\RouteNames;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 * @var ?AccountDTO $account
 */

$routes = $account ? [
    // RouteNames::COUNTERS,
    // RouteNames::BILLING,
] : [];
?>

@foreach($routes as $r)
    <li class="nav-item">
        <a class="nav-link"
           href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
    </li>
@endforeach
