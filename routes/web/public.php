<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\Public\PagesController::class, 'index'])->name(RouteNames::INDEX);
Route::get('/contacts', [Controllers\Public\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);

Route::get('/garbage', [Controllers\Public\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Public\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
Route::get('/terms', [Controllers\Public\PagesController::class, 'terms'])->name(RouteNames::TERMS);
Route::get('/personal-data-consent', [Controllers\Public\PagesController::class, 'personalDataConsent'])->name(RouteNames::PERSONAL_DATA_CONSENT);
Route::get('/payments-info', [Controllers\Public\PagesController::class, 'paymentsInfo'])->name(RouteNames::PAYMENTS_INFO);
Route::get('/cookie', [Controllers\Public\PagesController::class, 'cookiePolicy'])->name(RouteNames::COOKIE_POLICY);
Route::get('/regulation', [Controllers\Public\PagesController::class, 'regulation'])->name(RouteNames::REGULATION);
Route::get('/search', [Controllers\Public\PagesController::class, 'search'])->name(RouteNames::SEARCH);

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
Breadcrumbs::for(RouteNames::SEARCH, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::SEARCH), route(RouteNames::SEARCH));
});
Breadcrumbs::for(RouteNames::PRIVACY, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::PRIVACY), route(RouteNames::PRIVACY));
});
Breadcrumbs::for(RouteNames::TERMS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::TERMS), route(RouteNames::TERMS));
});
Breadcrumbs::for(RouteNames::PERSONAL_DATA_CONSENT, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::PERSONAL_DATA_CONSENT), route(RouteNames::PERSONAL_DATA_CONSENT));
});
Breadcrumbs::for(RouteNames::PAYMENTS_INFO, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::PAYMENTS_INFO), route(RouteNames::PAYMENTS_INFO));
});
Breadcrumbs::for(RouteNames::COOKIE_POLICY, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::INDEX);
    $trail->push(RouteNames::name(RouteNames::COOKIE_POLICY), route(RouteNames::COOKIE_POLICY));
});

Route::get('/token/{token}', [Controllers\TokenController::class, 'token'])->name(RouteNames::TOKEN);

Route::group(['prefix' => 'search'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/search', [Controllers\Public\SearchController::class, 'search'])->name(RouteNames::SITE_SEARCH);
    });
});
