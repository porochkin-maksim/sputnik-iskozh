<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Resources\RouteNames;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controllers\Public\PagesController::class, 'index'])->name(RouteNames::INDEX);
Route::get('/contacts', [Controllers\Public\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);

Route::get('/garbage', [Controllers\Public\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Public\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
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

Route::get('/token/{token}', [Controllers\TokenController::class, 'token'])->name(RouteNames::TOKEN);

Route::group(['prefix' => 'search'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/search', [Controllers\Public\SearchController::class, 'search'])->name(RouteNames::SITE_SEARCH);
    });
});
