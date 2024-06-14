<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Core\Resources\Views\ViewNames;
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

Route::group(['prefix' => 'home'], function () {
    Route::group(['middleware' => MiddlewareNames::AUTH], function () {
        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::get('/', [Controllers\Account\AccountsController::class, 'index'])->name(RouteNames::HOME);
            Route::post('/register', Controllers\Account\RegisterController::class)->name(RouteNames::ACCOUNT_REGISTER);
            Route::get('/profile', [Controllers\Account\ProfileController::class, 'show'])->name(RouteNames::PROFILE);
            Route::post('/profile', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::PROFILE_SAVE);
            Route::get('/COUNTERS', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::COUNTERS);
            Route::get('/BILLING', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::BILLING);
        });
    });
});

Route::group(['prefix' => 'reports'], function () {
    Route::get('/', [Controllers\Reports\ReportsController::class, 'index'])->name(RouteNames::REPORTS);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\Reports\ReportsController::class, 'list'])->name(RouteNames::REPORTS_LIST);
        Route::get('/create', [Controllers\Reports\ReportsController::class, 'create'])->name(RouteNames::REPORTS_CREATE);
        Route::post('/save', [Controllers\Reports\ReportsController::class, 'save'])->name(RouteNames::REPORTS_SAVE);
        Route::get('/edit/{id}', [Controllers\Reports\ReportsController::class, 'edit'])->name(RouteNames::REPORTS_EDIT);
        Route::delete('/delete/{id}', [Controllers\Reports\ReportsController::class, 'delete'])->name(RouteNames::REPORTS_DELETE);
        Route::post('/file/upload/{id}', [Controllers\Reports\ReportsController::class, 'uploadFile'])->name(RouteNames::REPORTS_FILE_UPLOAD);
        Route::post('/file/delete/{id}', [Controllers\FileController::class, 'delete'])->name(RouteNames::REPORTS_FILE_DELETE);
    });
});

Route::group(['prefix' => 'news'], function () {
    Route::get('/', [Controllers\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\News\NewsController::class, 'show'])->name(RouteNames::NEWS_SHOW);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/create', [Controllers\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [Controllers\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [Controllers\News\NewsController::class, 'edit'])->name(RouteNames::NEWS_EDIT);
        Route::delete('/delete/{id}', [Controllers\News\NewsController::class, 'delete'])->name(RouteNames::NEWS_DELETE);
        Route::post('/file/upload/{id}', [Controllers\News\NewsController::class, 'uploadFile'])->name(RouteNames::NEWS_FILE_UPLOAD);
        Route::post('/file/delete/{id}', [Controllers\FileController::class, 'delete'])->name(RouteNames::NEWS_FILE_DELETE);
    });
});

Route::group(['prefix' => 'files'], function () {
    Route::get('/', [Controllers\FileController::class, 'index'])->name(RouteNames::FILES);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\FileController::class, 'list'])->name(RouteNames::FILES_LIST);
        Route::post('/store', [Controllers\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
        Route::post('/save', [Controllers\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
        Route::get('/edit/{id}', [Controllers\FileController::class, 'edit'])->name(RouteNames::FILES_EDIT);
        Route::delete('/delete/{id}', [Controllers\FileController::class, 'delete'])->name(RouteNames::FILES_DELETE);
    });
});
