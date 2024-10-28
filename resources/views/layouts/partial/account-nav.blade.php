<?php declare(strict_types=1);

use Core\Domains\Access\Enums\Permission;
use Core\Resources\RouteNames;

$routes = [];

$routes[] = Permission::canEditOptions(app::user()->getRole()) ? RouteNames::OPTIONS : null;
?>

@foreach($routes as $r)
    @if ($r)
        <li class="nav-item">
            <a class="nav-link" href="{{ route($r) }}">{{ RouteNames::name($r) }}</a>
        </li>
    @endif
@endforeach
