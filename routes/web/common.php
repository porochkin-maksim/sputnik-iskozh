<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'news'], static function () {
    Route::get('/', [Controllers\Pages\News\NewsController::class, 'index'])->name(RouteNames::NEWS);
    Route::get('/{id}', [Controllers\Pages\News\NewsController::class, 'show'])
        ->name(RouteNames::NEWS_SHOW)
        ->whereNumber('id')
    ;
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Pages\News\NewsController::class, 'list'])->name(RouteNames::NEWS_LIST);
        Route::get('/list/index', [Controllers\Pages\News\NewsController::class, 'indexList'])->name(RouteNames::NEWS_INDEX_LIST);
        Route::get('/list/locked', [Controllers\Pages\News\NewsController::class, 'lockedNews'])->name(RouteNames::NEWS_LIST_LOCKED);
        Route::get('/create', [Controllers\Pages\News\NewsController::class, 'create'])->name(RouteNames::NEWS_CREATE);
        Route::post('/save', [Controllers\Pages\News\NewsController::class, 'save'])->name(RouteNames::NEWS_SAVE);
        Route::get('/edit/{id}', [Controllers\Pages\News\NewsController::class, 'edit'])
            ->name(RouteNames::NEWS_EDIT)
            ->whereNumber('id')
        ;
        Route::delete('/delete/{id}', [Controllers\Pages\News\NewsController::class, 'delete'])
            ->name(RouteNames::NEWS_DELETE)
            ->whereNumber('id')
        ;
        Route::post('/file/save', [Controllers\Pages\News\NewsController::class, 'saveFile'])->name(RouteNames::NEWS_FILE_SAVE);
        Route::post('/file/upload/{id}', [Controllers\Pages\News\NewsController::class, 'uploadFile'])
            ->name(RouteNames::NEWS_FILE_UPLOAD)
            ->whereNumber('id')
        ;
        Route::delete('/file/delete/{id}', [Controllers\Pages\News\NewsController::class, 'deleteFile'])
            ->name(RouteNames::NEWS_FILE_DELETE)
            ->whereNumber('id')
        ;
    });
});

Route::group(['prefix' => 'announcements'], static function () {
    Route::get('/', [Controllers\Pages\News\AnnouncementController::class, 'index'])->name(RouteNames::ANNOUNCEMENTS);
    Route::get('/{id}', [Controllers\Pages\News\AnnouncementController::class, 'show'])
        ->name(RouteNames::ANNOUNCEMENTS_SHOW)
        ->whereNumber('id')
    ;
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Pages\News\AnnouncementController::class, 'list'])->name(RouteNames::ANNOUNCEMENTS_LIST);
    });
});

Route::group(['prefix' => 'document'], static function () {
    Route::get('/invoice-receipt/blank', [Controllers\Common\Documents\ReceiptController::class, 'makeForBlank'])->name(RouteNames::DOCUMENT_RECEIPT_BLANK);
    Route::get('/invoice-receipt/{uid}', [Controllers\Common\Documents\ReceiptController::class, 'makeForInvoice'])->name(RouteNames::DOCUMENT_RECEIPT_INVOICE);
});

Route::group(['prefix' => 'folders'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Pages\Files\FolderController::class, 'list'])->name(RouteNames::FOLDERS_LIST);
        Route::get('/show/{id}', [Controllers\Pages\Files\FolderController::class, 'show'])
            ->name(RouteNames::FOLDERS_SHOW)
            ->whereNumber('id')
        ;
        Route::get('/info/{id?}', [Controllers\Pages\Files\FolderController::class, 'info'])->name(RouteNames::FOLDERS_INFO);

        Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
            Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
                Route::post('/save', [Controllers\Pages\Files\FolderController::class, 'save'])->name(RouteNames::FOLDERS_SAVE);
                Route::delete('/delete/{id}', [Controllers\Pages\Files\FolderController::class, 'delete'])
                    ->name(RouteNames::FOLDERS_DELETE)
                    ->whereNumber('id')
                ;
            });
        });
    });
});

Route::group(['prefix' => 'files'], static function () {
    Route::get('/{folder?}', [Controllers\Pages\Files\FolderController::class, 'index'])->name(RouteNames::FILES);

    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Pages\Files\FileController::class, 'list'])->name(RouteNames::FILES_LIST);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
            Route::post('/store', [Controllers\Pages\Files\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
            Route::post('/save', [Controllers\Pages\Files\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
            Route::post('/replace', [Controllers\Pages\Files\FileController::class, 'replace'])->name(RouteNames::FILES_REPLACE);
            Route::post('/up/{id}', [Controllers\Pages\Files\FileController::class, 'up'])
                ->name(RouteNames::FILES_UP)
                ->whereNumber('id')
            ;
            Route::post('/down/{id}', [Controllers\Pages\Files\FileController::class, 'down'])
                ->name(RouteNames::FILES_DOWN)
                ->whereNumber('id')
            ;
            Route::post('/move', [Controllers\Pages\Files\FileController::class, 'move'])->name(RouteNames::FILES_MOVE);
            Route::get('/edit/{id}', [Controllers\Pages\Files\FileController::class, 'edit'])
                ->name(RouteNames::FILES_EDIT)
                ->whereNumber('id')
            ;
            Route::delete('/delete/{id}', [Controllers\Pages\Files\FileController::class, 'delete'])
                ->name(RouteNames::FILES_DELETE)
                ->whereNumber('id')
            ;
        });
    });
});

Route::get('/storage/{filePath}', [App\Http\Controllers\FileController::class, 'download'])->where('filePath', '.*');
