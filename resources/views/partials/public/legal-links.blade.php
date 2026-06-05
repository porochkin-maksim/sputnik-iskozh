<?php declare(strict_types=1);

use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

?>

@php
    $links = [
        RouteNames::PRIVACY,
        RouteNames::TERMS,
        RouteNames::PERSONAL_DATA_CONSENT,
        RouteNames::PAYMENTS_INFO,
        RouteNames::COOKIE_POLICY,
    ];
@endphp

<nav class="footer-legal-links"
     aria-label="Юридическая информация">
    <div class="footer-legal-links-title">
        Юридическая информация
    </div>
    @foreach($links as $routeName)
        <a href="{{ route($routeName) }}"
           class="footer-legal-link {{ Route::is($routeName) ? 'is-active' : '' }}">
            {{ RouteNames::name($routeName) }}
        </a>
    @endforeach
</nav>
