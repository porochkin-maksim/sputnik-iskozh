<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'news'], static function () {
    Route::get('/', [Controllers\Public\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\Public\News\NewsController::class, 'show'])
        ->name(RouteNames::NEWS_SHOW)
        ->whereNumber('id')
    ;
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Public\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/list/index', [Controllers\Public\News\NewsController::class, 'indexList'])->name(RouteNames::NEWS_INDEX_LIST);
        Route::get('/list/locked', [Controllers\Public\News\NewsController::class, 'lockedNews'])->name(RouteNames::NEWS_LIST_LOCKED);
        Route::get('/create', [Controllers\Public\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [Controllers\Public\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [Controllers\Public\News\NewsController::class, 'edit'])
            ->name(RouteNames::NEWS_EDIT)
            ->whereNumber('id')
        ;
        Route::delete('/delete/{id}', [Controllers\Public\News\NewsController::class, 'delete'])
            ->name(RouteNames::NEWS_DELETE)
            ->whereNumber('id')
        ;
        Route::post('/file/save', [Controllers\Public\News\NewsController::class, 'saveFile'])->name(RouteNames::NEWS_FILE_SAVE);
        Route::post('/file/upload/{id}', [Controllers\Public\News\NewsController::class, 'uploadFile'])
            ->name(RouteNames::NEWS_FILE_UPLOAD)
            ->whereNumber('id')
        ;
        Route::delete('/file/delete/{id}', [Controllers\Public\News\NewsController::class, 'deleteFile'])
            ->name(RouteNames::NEWS_FILE_DELETE)
            ->whereNumber('id')
        ;
    });
});

Route::group(['prefix' => 'announcements'], static function () {
    Route::get('/', [Controllers\Public\News\AnnouncementController::class, 'index'])->name(RouteNames::ANNOUNCEMENTS);
    Route::get('/{id}', [Controllers\Public\News\AnnouncementController::class, 'show'])
        ->name(RouteNames::ANNOUNCEMENTS_SHOW)
        ->whereNumber('id')
    ;
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Public\News\AnnouncementController::class, 'list'])->name(RouteNames::ANNOUNCEMENTS_LIST);
    });
});
