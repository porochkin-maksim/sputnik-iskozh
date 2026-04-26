<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'periods'], static function () {
    Route::get('/', [Controllers\Admin\Billing\PeriodController::class, 'index'])->name(RouteNames::ADMIN_PERIOD_INDEX);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/create', [Controllers\Admin\Billing\PeriodController::class, 'create'])->name(RouteNames::ADMIN_PERIOD_CREATE);
        Route::get('/list', [Controllers\Admin\Billing\PeriodController::class, 'list'])->name(RouteNames::ADMIN_PERIOD_LIST);
        Route::post('/save', [Controllers\Admin\Billing\PeriodController::class, 'save'])->name(RouteNames::ADMIN_PERIOD_SAVE);
        Route::delete('/{id}', [Controllers\Admin\Billing\PeriodController::class, 'delete'])
            ->name(RouteNames::ADMIN_PERIOD_DELETE)
            ->whereNumber('id')
        ;
    });
});
