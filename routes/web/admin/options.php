<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'options'], static function () {
    Route::get('/', [Controllers\Admin\System\OptionsController::class, 'index'])->name(RouteNames::ADMIN_OPTIONS_INDEX);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Admin\System\OptionsController::class, 'list'])->name(RouteNames::ADMIN_OPTIONS_LIST);
        Route::post('/save', [Controllers\Admin\System\OptionsController::class, 'save'])->name(RouteNames::ADMIN_OPTIONS_SAVE);
    });
});
