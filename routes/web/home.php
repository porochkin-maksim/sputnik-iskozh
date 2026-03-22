<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'home'], static function () {
    Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
        Route::get('/', [Controllers\Profile\HomeController::class, 'index'])->name(RouteNames::HOME);

        Route::group(['prefix' => 'profile'], static function () {
            Route::post('/password', [Controllers\Profile\ProfileController::class, 'savePassword'])->name(RouteNames::PROFILE_SAVE_PASSWORD);
            Route::post('/switch-account', [Controllers\Profile\ProfileController::class, 'switchAccount'])->name(RouteNames::PROFILE_SWITCH_ACCOUNT);
        });

        Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
            Route::post('/acquring/create/{invoiceId}/{amount}', [Controllers\Pages\Requests\AcquringController::class, 'create'])
                ->name(RouteNames::ACQURING_INVOICE_CREATE)
                ->whereNumber('invoiceId')
            ;

            Route::group(['prefix' => 'counters'], static function () {
                Route::get('/', [Controllers\Profile\CounterController::class, 'index'])->name(RouteNames::PROFILE_COUNTERS);
                Route::group(['prefix' => 'json'], static function () {
                    Route::get('/list', [Controllers\Profile\CounterController::class, 'list'])->name(RouteNames::PROFILE_COUNTERS_LIST);
                    Route::post('/create', [Controllers\Profile\CounterController::class, 'create'])->name(RouteNames::PROFILE_COUNTER_CREATE);
                    Route::post('/increment', [Controllers\Profile\CounterController::class, 'incrementSave'])->name(RouteNames::PROFILE_COUNTER_INCREMENT);
                    Route::post('/add-value', [Controllers\Profile\CounterController::class, 'addValue'])->name(RouteNames::PROFILE_COUNTER_ADD_VALUE);
                    Route::post('/history', [Controllers\Profile\CounterController::class, 'history'])->name(RouteNames::PROFILE_COUNTER_HISTORY);
                });
                Route::get('/{counter}', [Controllers\Profile\CounterController::class, 'view'])->name(RouteNames::PROFILE_COUNTER_VIEW);
            });
            Route::group(['prefix' => 'invoices'], static function () {
                Route::get('/', [Controllers\Profile\HomeController::class, 'invoices'])->name(RouteNames::PROFILE_INVOICES);
            });
        });
    });
});