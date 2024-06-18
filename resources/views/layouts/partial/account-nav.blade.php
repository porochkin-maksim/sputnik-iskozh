<?php declare(strict_types=1);

use Core\Domains\Access\Enums\Permission;
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\User\Models\UserDTO;
use Core\Resources\RouteNames;

/**
 * @see https://iqbalfn.github.io/bootstrap-vertical-menu/
 *
 * @var ?AccountDTO $account
 * @var ?UserDTO    $user
 */
$routes = [];

$routes[] = Permission::canEditOptions($user?->getRole()) ? RouteNames::OPTIONS : null;
?>

@foreach($routes as $r)
    @if ($r)
        <li class="nav-item">
            <a class="nav-link" href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
        </li>
    @endif
@endforeach
