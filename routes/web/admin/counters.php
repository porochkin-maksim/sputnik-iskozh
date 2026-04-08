<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'counters'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::group(['prefix' => '{counterId}'], static function () {
            Route::group(['prefix' => 'history'], static function () {
                Route::get('/list', [Controllers\Admin\Account\CounterHistoryController::class, 'list'])
                    ->name(RouteNames::ADMIN_COUNTER_HISTORY_LIST)
                    ->whereNumber('counterId')
                ;
            });
        });
    });
});

// необработанные показания счётчиков
Route::group(['prefix' => 'counter-history'], static function () {
    Route::get('/', [Controllers\Admin\Account\CounterController::class, 'index'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_INDEX);
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/create-claim/{historyId}', [Controllers\Admin\Account\CounterController::class, 'createClaim'])
            ->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CREATE_CLAIM)
            ->whereNumber('historyId')
        ;
        Route::get('/list', [Controllers\Admin\Requests\CounterController::class, 'list'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_LIST);
        Route::post('/link', [Controllers\Admin\Requests\CounterController::class, 'link'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_LINK);
        Route::delete('/delete/{historyId}', [Controllers\Admin\Requests\CounterController::class, 'delete'])
            ->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_DELETE)
            ->whereNumber('historyId')
        ;
        Route::post('/confirm', [Controllers\Admin\Requests\CounterController::class, 'confirm'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM);
        Route::post('/confirm-delete', [Controllers\Admin\Requests\CounterController::class, 'confirmDelete'])->name(RouteNames::ADMIN_REQUEST_COUNTER_HISTORY_CONFIRM_DELETE);
    });
});