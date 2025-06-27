<?php declare(strict_types=1);
// routes/breadcrumbs.php

// https://github.com/diglactic/laravel-breadcrumbs

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Resources\RouteNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for(RouteNames::ADMIN, static function (BreadcrumbTrail $trail) {
    $trail->push('Главная', route(RouteNames::ADMIN));
});

Breadcrumbs::for(RouteNames::ADMIN_INVOICE_INDEX, static function (BreadcrumbTrail $trail) {
    $previousUrl = url()->previous();
    $routeName   = Route::getRoutes()->match(Request::create($previousUrl))->getName();
    $route       = route(RouteNames::ADMIN_INVOICE_INDEX);
    if ($routeName === RouteNames::ADMIN_INVOICE_INDEX) {
        $route = $previousUrl;
    }

    $trail->parent(RouteNames::ADMIN);
    $trail->push(RouteNames::name(RouteNames::ADMIN_INVOICE_INDEX), $route);
});
Breadcrumbs::for(RouteNames::ADMIN_INVOICE_VIEW, static function (BreadcrumbTrail $trail, InvoiceDTO $invoice) {
    $trail->parent(RouteNames::ADMIN_INVOICE_INDEX);
    $trail->push('Счёт №' . $invoice->getId(), route(RouteNames::ADMIN_INVOICE_VIEW, $invoice->getId()));
});