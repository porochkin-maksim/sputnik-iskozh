<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'services'], static function () {
    Route::get('/', [Controllers\Admin\Billing\ServiceController::class, 'index'])->name(RouteNames::ADMIN_SERVICE_INDEX);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/create', [Controllers\Admin\Billing\ServiceController::class, 'create'])->name(RouteNames::ADMIN_SERVICE_CREATE);
        Route::get('/list', [Controllers\Admin\Billing\ServiceController::class, 'list'])->name(RouteNames::ADMIN_SERVICE_LIST);
        Route::post('/save', [Controllers\Admin\Billing\ServiceController::class, 'save'])->name(RouteNames::ADMIN_SERVICE_SAVE);
        Route::delete('/{id}', [Controllers\Admin\Billing\ServiceController::class, 'delete'])
            ->name(RouteNames::ADMIN_SERVICE_DELETE)
            ->whereNumber('id')
        ;
    });
});
