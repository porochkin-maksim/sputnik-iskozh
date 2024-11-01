<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include __DIR__ . '/web/auth.php';
include __DIR__ . '/web/session.php';

Route::get('/', [Controllers\Pages\PagesController::class, 'index'])->name(RouteNames::INDEX);

Route::get('/contacts', [Controllers\Pages\PagesController::class, 'contacts'])->name(RouteNames::CONTACTS);
Route::get('/contacts/proposal', [Controllers\Pages\PagesController::class, 'proposal'])->name(RouteNames::PROPOSAL);
Route::post('/contacts/proposal', [Controllers\Proposal\ProposalController::class, 'create'])->name(RouteNames::PROPOSAL_CREATE);

Route::get('/garbage', [Controllers\Pages\PagesController::class, 'garbage'])->name(RouteNames::GARBAGE);
Route::get('/privacy', [Controllers\Pages\PagesController::class, 'privacy'])->name(RouteNames::PRIVACY);
Route::get('/regulation', [Controllers\Pages\PagesController::class, 'regulation'])->name(RouteNames::REGULATION);
Route::get('/rubrics', [Controllers\Pages\PagesController::class, 'rubrics'])->name(RouteNames::RUBRICS);

Route::group(['prefix' => 'pages'], function () {
    Route::group(['prefix' => 'json'], function () {
        Route::post('/edit', [Controllers\Pages\TemplateController::class, 'get'])->name(RouteNames::TEMPLATE_GET);
        Route::patch('/edit', [Controllers\Pages\TemplateController::class, 'update'])->name(RouteNames::TEMPLATE_UPDATE);
    });
});

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
            Route::post('/file/delete/{id}', [Controllers\Files\FileController::class, 'delete'])->name(RouteNames::REPORTS_FILE_DELETE);
        });
    });
});

Route::group(['prefix' => 'news'], function () {
    Route::get('/', [Controllers\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\News\NewsController::class, 'show'])->name(RouteNames::NEWS_SHOW);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/list/all', [Controllers\News\NewsController::class, 'listAll'])->name(RouteNames::NEWS_LIST_ALL);
        Route::get('/create', [Controllers\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [Controllers\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [Controllers\News\NewsController::class, 'edit'])->name(RouteNames::NEWS_EDIT);
        Route::delete('/delete/{id}', [Controllers\News\NewsController::class, 'delete'])->name(RouteNames::NEWS_DELETE);
        Route::post('/file/save', [Controllers\News\NewsController::class, 'saveFile'])->name(RouteNames::NEWS_FILE_SAVE);
        Route::post('/file/upload/{id}', [Controllers\News\NewsController::class, 'uploadFile'])->name(RouteNames::NEWS_FILE_UPLOAD);
        Route::delete('/file/delete/{id}', [Controllers\News\NewsController::class, 'deleteFile'])->name(RouteNames::NEWS_FILE_DELETE);
    });
});
Route::group(['prefix' => 'announcements'], function () {
    Route::get('/', [Controllers\News\AnnouncementController::class, 'index'])->name(RouteNames::ANNOUNCEMENTS);
    Route::get('/{id}', [Controllers\News\AnnouncementController::class, 'show'])->name(RouteNames::ANNOUNCEMENTS_SHOW);
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\News\AnnouncementController::class, 'list'])->name(RouteNames::ANNOUNCEMENTS_LIST);
    });
});

Route::group(['prefix' => 'files'], function () {
    Route::get('/{folder?}', [Controllers\Files\FolderController::class, 'index'])->name(RouteNames::FILES);

    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\Files\FileController::class, 'list'])->name(RouteNames::FILES_LIST);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::post('/store', [Controllers\Files\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
            Route::post('/save', [Controllers\Files\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
            Route::post('/replace', [Controllers\Files\FileController::class, 'replace'])->name(RouteNames::FILES_REPLACE);
            Route::post('/up/{id}', [Controllers\Files\FileController::class, 'up'])->name(RouteNames::FILES_UP);
            Route::post('/down/{id}', [Controllers\Files\FileController::class, 'down'])->name(RouteNames::FILES_DOWN);
            Route::post('/move', [Controllers\Files\FileController::class, 'move'])->name(RouteNames::FILES_MOVE);
            Route::get('/edit/{id}', [Controllers\Files\FileController::class, 'edit'])->name(RouteNames::FILES_EDIT);
            Route::delete('/delete/{id}', [Controllers\Files\FileController::class, 'delete'])->name(RouteNames::FILES_DELETE);
        });
    });
});

Route::group(['prefix' => 'folders'], function () {
    Route::group(['prefix' => 'json'], function () {
        Route::get('/list', [Controllers\Files\FolderController::class, 'list'])->name(RouteNames::FOLDERS_LIST);
        Route::get('/show/{id}', [Controllers\Files\FolderController::class, 'show'])->name(RouteNames::FOLDERS_SHOW);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], function () {
            Route::post('/save', [Controllers\Files\FolderController::class, 'save'])->name(RouteNames::FOLDERS_SAVE);
            Route::delete('/delete/{id}', [Controllers\Files\FolderController::class, 'delete'])->name(RouteNames::FOLDERS_DELETE);
        });
    });
});
