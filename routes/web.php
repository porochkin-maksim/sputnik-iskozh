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
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [App\Http\Controllers\Reports\ReportsController::class, 'list'])->name(RouteNames::REPORTS_LIST);
        Route::get('/create', [App\Http\Controllers\Reports\ReportsController::class, 'create'])->name(RouteNames::REPORTS_CREATE);
        Route::post('/save', [App\Http\Controllers\Reports\ReportsController::class, 'save'])->name(RouteNames::REPORTS_SAVE);
        Route::get('/edit/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'edit'])->name(RouteNames::REPORTS_EDIT);
        Route::delete('/delete/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'delete'])->name(RouteNames::REPORTS_DELETE);
        Route::post('/file/upload/{id}', [App\Http\Controllers\Reports\ReportsController::class, 'uploadFile'])->name(RouteNames::REPORTS_FILE_UPLOAD);
        Route::post('/file/delete/{id}', [App\Http\Controllers\FileController::class, 'delete'])->name(RouteNames::REPORTS_FILE_DELETE);
    });
});

Route::group(['prefix' => 'news'], function () {
    Route::get('/', [App\Http\Controllers\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [App\Http\Controllers\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/create', [App\Http\Controllers\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [App\Http\Controllers\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [App\Http\Controllers\News\NewsController::class, 'edit'])->name(RouteNames::NEWS_EDIT);
        Route::delete('/delete/{id}', [App\Http\Controllers\News\NewsController::class, 'delete'])->name(RouteNames::NEWS_DELETE);
        Route::post('/file/upload/{id}', [App\Http\Controllers\News\NewsController::class, 'uploadFile'])->name(RouteNames::NEWS_FILE_UPLOAD);
        Route::post('/file/delete/{id}', [App\Http\Controllers\FileController::class, 'delete'])->name(RouteNames::NEWS_FILE_DELETE);
    });
});

Route::group(['prefix' => 'files'], function () {
    Route::get('/', [App\Http\Controllers\FileController::class, 'index'])->name(RouteNames::FILES);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [App\Http\Controllers\FileController::class, 'list'])->name(RouteNames::FILES_LIST);
        Route::post('/store', [App\Http\Controllers\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
        Route::post('/save', [App\Http\Controllers\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
        Route::get('/edit/{id}', [App\Http\Controllers\FileController::class, 'edit'])->name(RouteNames::FILES_EDIT);
        Route::delete('/delete/{id}', [App\Http\Controllers\FileController::class, 'delete'])->name(RouteNames::FILES_DELETE);
    });
});
