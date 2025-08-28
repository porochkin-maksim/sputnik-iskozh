<?php declare(strict_types=1);
// routes/breadcrumbs.php

// https://github.com/diglactic/laravel-breadcrumbs

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\Models\NewsDTO;
use Core\Resources\RouteNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// ЛК
Breadcrumbs::for(RouteNames::HOME, static function (BreadcrumbTrail $trail) {
    $trail->push('Мой кабинет', route(RouteNames::HOME));
});
Breadcrumbs::for(RouteNames::PROFILE_INVOICES, static function (BreadcrumbTrail $trail, ?PeriodDTO $period = null) {
    $trail->parent(RouteNames::HOME);
    $name = RouteNames::name(RouteNames::PROFILE_INVOICES);
    $trail->push($period ? sprintf('%s периода "%s"', $name, $period->getName()) : $name);
});
Breadcrumbs::for(RouteNames::PROFILE_COUNTERS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::HOME);
    $trail->push(RouteNames::name(RouteNames::PROFILE_COUNTERS), route(RouteNames::PROFILE_COUNTERS));
});
Breadcrumbs::for(RouteNames::PROFILE_COUNTER_VIEW, static function (BreadcrumbTrail $trail, CounterDTO $counter) {
    $trail->parent(RouteNames::PROFILE_COUNTERS);
    $trail->push('Счётчик ' . $counter->getNumber(), route(RouteNames::PROFILE_COUNTER_VIEW, [$counter->getUid()]));
});

// публичная часть
Breadcrumbs::for(RouteNames::INDEX, static function (BreadcrumbTrail $trail) {
    $trail->push(RouteNames::name(RouteNames::INDEX), route(RouteNames::INDEX));
});
Breadcrumbs::for(RouteNames::ANNOUNCEMENTS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::ANNOUNCEMENTS), route(RouteNames::ANNOUNCEMENTS));
});
Breadcrumbs::for(RouteNames::NEWS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::NEWS), route(RouteNames::NEWS));
});
Breadcrumbs::for(RouteNames::NEWS_SHOW, static function (BreadcrumbTrail $trail, NewsDTO $news) {
    if ($news->getCategory() === CategoryEnum::ANNOUNCEMENT) {
        $trail->parent(RouteNames::ANNOUNCEMENTS);
    }else{
        $trail->parent(RouteNames::NEWS);
    }
    $trail->push('#' . $news->getId(), route(RouteNames::NEWS_SHOW, $news->getId()));
});
Breadcrumbs::for(RouteNames::GARBAGE, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::GARBAGE), route(RouteNames::GARBAGE));
});
Breadcrumbs::for(RouteNames::FILES, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::FILES), route(RouteNames::FILES));
});
Breadcrumbs::for(RouteNames::CONTACTS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::CONTACTS), route(RouteNames::CONTACTS));
});
Breadcrumbs::for(RouteNames::REQUESTS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::CONTACTS);
    $trail->push(RouteNames::name(RouteNames::REQUESTS), route(RouteNames::REQUESTS));
});
Breadcrumbs::for(RouteNames::REQUESTS_COUNTER, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::REQUESTS);
    $trail->push(RouteNames::name(RouteNames::REQUESTS_COUNTER), route(RouteNames::REQUESTS_COUNTER));
});
Breadcrumbs::for(RouteNames::REQUESTS_PROPOSAL, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::REQUESTS);
    $trail->push(RouteNames::name(RouteNames::REQUESTS_PROPOSAL), route(RouteNames::REQUESTS_PROPOSAL));
});
Breadcrumbs::for(RouteNames::REQUESTS_PAYMENT, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::REQUESTS);
    $trail->push(RouteNames::name(RouteNames::REQUESTS_PAYMENT), route(RouteNames::REQUESTS_PAYMENT));
});

// главная админки
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

// админка. участки
Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_INDEX, static function (BreadcrumbTrail $trail) {
    $trail->push('Участки', route(RouteNames::ADMIN_ACCOUNT_INDEX));
});
Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_VIEW, static function (BreadcrumbTrail $trail, AccountDTO $account) {
    $trail->parent(RouteNames::ADMIN_ACCOUNT_INDEX);
    $trail->push('Участок №' . $account->getNumber(), route(RouteNames::ADMIN_ACCOUNT_VIEW, $account->getId()));
});
Breadcrumbs::for(RouteNames::ADMIN_COUNTER_VIEW, static function (BreadcrumbTrail $trail, CounterDTO $counter) {
    $trail->parent(RouteNames::ADMIN_ACCOUNT_VIEW, $counter->getAccount(true));
    $trail->push('Счётчик №' . $counter->getNumber(), route(RouteNames::ADMIN_COUNTER_VIEW, [$counter->getAccountId(), $counter->getId()]));
});