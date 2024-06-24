<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';

Route::get('/', [Controllers\PagesController::class, 'index'])->name(RouteNames::INDEX);
Route::get('/contacts', [Controllers\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);
Route::get('/privacy', [Controllers\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);

Route::group(['prefix' => 'home'], function () {
    Route::group(['middleware' => MiddlewareNames::AUTH], function () {
        Route::get('/', [Controllers\Account\AccountsController::class, 'index'])->name(RouteNames::HOME);

        Route::group(['prefix' => 'profile'], function () {
            Route::get('/', [Controllers\Account\ProfileController::class, 'show'])->name(RouteNames::PROFILE);
            Route::post('/', [Controllers\Account\ProfileController::class, 'save'])->name(RouteNames::PROFILE_SAVE);
            Route::post('/email', [Controllers\Account\ProfileController::class, 'saveEmail'])->name(RouteNames::PROFILE_SAVE_EMAIL);
            Route::post('/password', [Controllers\Account\ProfileController::class, 'savePassword'])->name(RouteNames::PROFILE_SAVE_PASSWORD);
        });

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::get('/register', [Controllers\Account\RegisterController::class, 'index'])->name(RouteNames::ACCOUNT_REGISTER);
            Route::group(['prefix' => 'json'], function () {
                Route::post('/register', [Controllers\Account\RegisterController::class, 'register'])->name(RouteNames::ACCOUNT_REGISTER_SAVE);
                Route::get('/account-info', [Controllers\Account\AccountsController::class, 'info'])->name(RouteNames::ACCOUNT_INFO);
                Route::get('/counter/list', [Controllers\Account\CounterController::class, 'list'])->name(RouteNames::PROFILE_COUNTERS_LIST);
                Route::post('/counter', [Controllers\Account\CounterController::class, 'save'])->name(RouteNames::PROFILE_COUNTER_SAVE);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::AUTH], function () {
    Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
        Route::group(['prefix' => 'options'], function () {
            Route::get('/', [Controllers\Options\OptionsController::class, 'index'])->name(RouteNames::OPTIONS);
            Route::group(['prefix' => 'json'], function () {
                Route::get('/list', [Controllers\Options\OptionsController::class, 'list'])->name(RouteNames::OPTIONS_LIST);
                Route::post('/save', [Controllers\Options\OptionsController::class, 'save'])->name(RouteNames::OPTIONS_SAVE);
                Route::get('/edit/{id}', [Controllers\Options\OptionsController::class, 'edit'])->name(RouteNames::OPTIONS_EDIT);
            });
        });
    });
});

Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
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
        Route::post('/file/save', [Controllers\News\NewsController::class, 'saveFile'])->name(RouteNames::NEWS_FILE_SAVE);
        Route::post('/file/upload/{id}', [Controllers\News\NewsController::class, 'uploadFile'])->name(RouteNames::NEWS_FILE_UPLOAD);
        Route::delete('/file/delete/{id}', [Controllers\News\NewsController::class, 'deleteFile'])->name(RouteNames::NEWS_FILE_DELETE);
    });
});

Route::group(['prefix' => 'files'], function () {
    Route::get('/', [Controllers\FileController::class, 'index'])->name(RouteNames::FILES);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\FileController::class, 'list'])->name(RouteNames::FILES_LIST);
        Route::post('/store', [Controllers\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
        Route::post('/save', [Controllers\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
        Route::post('/up/{id}', [Controllers\FileController::class, 'up'])->name(RouteNames::FILES_UP);
        Route::post('/down/{id}', [Controllers\FileController::class, 'down'])->name(RouteNames::FILES_DOWN);
        Route::get('/edit/{id}', [Controllers\FileController::class, 'edit'])->name(RouteNames::FILES_EDIT);
        Route::delete('/delete/{id}', [Controllers\FileController::class, 'delete'])->name(RouteNames::FILES_DELETE);
    });
});
