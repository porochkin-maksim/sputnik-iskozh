<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

include 'common/news.php';
include 'common/documents.php';

Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
    Route::group(['prefix' => '/json/summary'], static function () {
        Route::get('/', [Controllers\Common\SummaryController::class, 'summary'])->name(RouteNames::SUMMARY);
        Route::get('/{type}', [Controllers\Common\SummaryController::class, 'summaryDetailing'])->name(RouteNames::SUMMARY_DETAILING);
    });
});

Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::prefix('selects')->name('selects.')->group(function () {
        Route::get('/accounts', [Controllers\Public\SelectsController::class, 'accounts'])->name('accounts');
    });
});

Route::group(['prefix' => 'contacts/requests'], static function () {
    include 'common/help-desk.php';

    Route::get('/', [Controllers\Public\RequestsPagesController::class, 'index'])->name(RouteNames::REQUESTS);
    Route::get('/proposal', [Controllers\Public\RequestsPagesController::class, 'proposal'])->name(RouteNames::REQUESTS_PROPOSAL);
    Route::post('/proposal', [Controllers\Public\Requests\ProposalController::class, 'create'])->name(RouteNames::REQUESTS_PROPOSAL_CREATE);
    Route::get('/payment', [Controllers\Public\RequestsPagesController::class, 'payment'])->name(RouteNames::REQUESTS_PAYMENT);
    Route::post('/payment', [Controllers\Public\Requests\PaymentsController::class, 'create'])->name(RouteNames::REQUESTS_PAYMENT_CREATE);
    Route::get('/counter', [Controllers\Public\RequestsPagesController::class, 'counter'])->name(RouteNames::REQUESTS_COUNTER);
    Route::post('/counter', [Controllers\Public\Requests\CounterController::class, 'create'])->name(RouteNames::REQUESTS_COUNTER_CREATE);
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

