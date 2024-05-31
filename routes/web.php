<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\ViewNames;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';

Route::get('/', function () {
    // if (Auth::check() || true) {
        return view(ViewNames::PAGES_INDEX);
    // }
    // else {
    //     return view(ViewNames::LAYOUTS_APP);
    // }
})->name(RouteNames::INDEX);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'reports'], function () {
    Route::get('/', [App\Http\Controllers\Reports\ReportsController::class, 'index'])->name(RouteNames::REPORTS);
    Route::get('/create', [App\Http\Controllers\Reports\ReportsController::class, 'create'])->name(RouteNames::REPORTS_CREATE);
    Route::get('/edit/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'edit'])->name(RouteNames::REPORTS_EDIT);
    Route::post('/save', [App\Http\Controllers\Reports\ReportsController::class, 'save'])->name(RouteNames::REPORTS_SAVE);
    Route::delete('/delete/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'delete'])->name(RouteNames::REPORTS_DELETE);
    Route::post('/list', [App\Http\Controllers\Reports\ReportsController::class, 'list'])->name(RouteNames::REPORTS_LIST);
    Route::post('/file/upload/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'uploadFile'])->name(RouteNames::REPORTS_FILE_UPLOAD);
    Route::post('/file/delete/{id}', [\App\Http\Controllers\FileController::class, 'delete'])->name(RouteNames::REPORTS_FILE_DELETE);
});
