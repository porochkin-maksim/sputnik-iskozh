<?php declare(strict_types=1);
// routes/breadcrumbs.php

// https://github.com/diglactic/laravel-breadcrumbs

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsEntity;
use App\Resources\RouteNames;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.

// ЛК
Breadcrumbs::for(RouteNames::HOME, static function (BreadcrumbTrail $trail) {
    $trail->push('Мой кабинет', route(RouteNames::HOME));
});
Breadcrumbs::for(RouteNames::PROFILE_INVOICES, static function (BreadcrumbTrail $trail, ?PeriodEntity $period = null) {
    $trail->parent(RouteNames::HOME);
    $name = RouteNames::name(RouteNames::PROFILE_INVOICES);
    $trail->push($period ? sprintf('%s периода "%s"', $name, $period->getName()) : $name);
});
Breadcrumbs::for(RouteNames::PROFILE_COUNTERS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::HOME);
    $trail->push(RouteNames::name(RouteNames::PROFILE_COUNTERS), route(RouteNames::PROFILE_COUNTERS));
});
Breadcrumbs::for(RouteNames::PROFILE_COUNTER_VIEW, static function (BreadcrumbTrail $trail, CounterEntity $counter) {
    $trail->parent(RouteNames::PROFILE_COUNTERS);
    $trail->push('Счётчик ' . $counter->getNumber(), route(RouteNames::PROFILE_COUNTER_VIEW, [$counter->getUid()]));
});
Breadcrumbs::for(RouteNames::PROFILE_PAYMENTS_INDEX, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::HOME);
    $trail->push('Платежи', route(RouteNames::PROFILE_PAYMENTS_INDEX));
});
Breadcrumbs::for(RouteNames::PROFILE_HELP_DESK_INDEX, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::HOME);
    $trail->push('Заявки', route(RouteNames::PROFILE_HELP_DESK_INDEX));
});
Breadcrumbs::for(RouteNames::PROFILE_HELP_DESK_VIEW, static function (BreadcrumbTrail $trail, \Core\Domains\HelpDesk\Models\TicketEntity $ticket) {
    $trail->parent(RouteNames::PROFILE_HELP_DESK_INDEX);
    $trail->push('Заявка №' . $ticket->getId());
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
Breadcrumbs::for(RouteNames::NEWS_SHOW, static function (BreadcrumbTrail $trail, NewsEntity $news) {
    if ($news->getCategory() === NewsCategoryEnum::ANNOUNCEMENT) {
        $trail->parent(RouteNames::ANNOUNCEMENTS);
    }
    else {
        $trail->parent(RouteNames::NEWS);
    }
    $trail->push('#' . $news->getId(), route(RouteNames::NEWS_SHOW, $news->getId()));
});
Breadcrumbs::for(RouteNames::NEWS_FORM, static function (BreadcrumbTrail $trail, ?NewsEntity $news = null) {
    $trail->parent(RouteNames::NEWS);
    $trail->push($news ? 'Редактировать' : 'Добавить');
});
