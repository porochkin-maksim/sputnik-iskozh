<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Controllers\TokenController;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';
include __DIR__ . '/web/session.php';
include __DIR__ . '/web/common.php';
include __DIR__ . '/web/home.php';
include __DIR__ . '/web/admin.php';

Route::get('/', [Controllers\Pages\PagesController::class, 'index'])->name(RouteNames::INDEX);
Route::get('/contacts', [Controllers\Pages\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);

Route::group(['prefix' => 'contacts/requests'], static function () {
    Route::get('/', [Controllers\Pages\RequestsPagesController::class, 'index'])->name(RouteNames::REQUESTS);
    Route::get('/proposal', [Controllers\Pages\RequestsPagesController::class, 'proposal'])->name(RouteNames::REQUESTS_PROPOSAL);
    Route::post('/proposal', [Controllers\Pages\Requests\ProposalController::class, 'create'])->name(RouteNames::REQUESTS_PROPOSAL_CREATE);
    Route::get('/payment', [Controllers\Pages\RequestsPagesController::class, 'payment'])->name(RouteNames::REQUESTS_PAYMENT);
    Route::post('/payment', [Controllers\Pages\Requests\PaymentsController::class, 'create'])->name(RouteNames::REQUESTS_PAYMENT_CREATE);
    Route::get('/counter', [Controllers\Pages\RequestsPagesController::class, 'counter'])->name(RouteNames::REQUESTS_COUNTER);
    Route::post('/counter', [Controllers\Pages\Requests\CounterController::class, 'create'])->name(RouteNames::REQUESTS_COUNTER_CREATE);
});

Route::get('/garbage', [Controllers\Pages\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Pages\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
Route::get('/regulation', [Controllers\Pages\PagesController::class, 'regulation'])->name(RouteNames::REGULATION);
Route::get('/search', [Controllers\Pages\PagesController::class, 'search'])->name(RouteNames::SEARCH);

Route::group(['prefix' => 'pages'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/edit', [Controllers\Pages\TemplateController::class, 'get'])->name(RouteNames::TEMPLATE_GET);
        Route::patch('/edit', [Controllers\Pages\TemplateController::class, 'update'])->name(RouteNames::TEMPLATE_UPDATE);
    });
});

Route::get('/token/{token}', [TokenController::class, 'token'])->name(RouteNames::TOKEN);

Route::group(['prefix' => 'search'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/search', [Controllers\Pages\SearchController::class, 'search'])->name(RouteNames::SITE_SEARCH);
    });
});

Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
    Route::group(['prefix' => '/json/summary'], static function () {
        Route::get('/', [Controllers\Common\SummaryController::class, 'summary'])->name(RouteNames::SUMMARY);
        Route::get('/{type}', [Controllers\Common\SummaryController::class, 'summaryDetailing'])->name(RouteNames::SUMMARY_DETAILING);
    });
});

Route::group(['prefix' => 'webhook'], static function () {
    Route::group(['prefix' => 'acquring'], static function () {
        Route::any('/submit/{acquringId}/{salt}', [Controllers\Webhook\AcquiringController::class, 'submit'])
            ->name(RouteNames::WEBHOOK_ACQURING_SUBMIT)
            ->whereNumber('acquringId')
        ;
        Route::any('/failed/{acquringId}/{salt}', [Controllers\Webhook\AcquiringController::class, 'failed'])
            ->name(RouteNames::WEBHOOK_ACQURING_FAILED)
            ->whereNumber('acquringId')
        ;
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Route::get('reestr/read', [\App\Http\Controllers\Admin\ReestrController::class, 'read'])->name('reestr.read');
});