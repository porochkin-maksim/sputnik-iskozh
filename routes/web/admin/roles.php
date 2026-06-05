<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles'], static function () {
    Route::get('/', [Controllers\Admin\System\RolesController::class, 'index'])->name(RouteNames::ADMIN_ROLE_INDEX);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/create', [Controllers\Admin\System\RolesController::class, 'create'])->name(RouteNames::ADMIN_ROLE_CREATE);
        Route::get('/list', [Controllers\Admin\System\RolesController::class, 'list'])->name(RouteNames::ADMIN_ROLE_LIST);
        Route::post('/save', [Controllers\Admin\System\RolesController::class, 'save'])->name(RouteNames::ADMIN_ROLE_SAVE);
        Route::delete('/{id}', [Controllers\Admin\System\RolesController::class, 'delete'])
            ->name(RouteNames::ADMIN_ROLE_DELETE)
            ->whereNumber('id')
        ;
    });
});
