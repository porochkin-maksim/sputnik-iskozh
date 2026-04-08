<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Middleware\Enums\MiddlewareNames;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'document'], static function () {
    Route::get('/invoice-receipt/blank', [Controllers\Common\Documents\ReceiptController::class, 'makeForBlank'])->name(RouteNames::DOCUMENT_RECEIPT_BLANK);
    Route::get('/invoice-receipt/{uid}', [Controllers\Common\Documents\ReceiptController::class, 'makeForInvoice'])->name(RouteNames::DOCUMENT_RECEIPT_INVOICE);
});

Route::group(['prefix' => 'folders'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Public\Files\FolderController::class, 'list'])->name(RouteNames::FOLDERS_LIST);
        Route::get('/show/{id}', [Controllers\Public\Files\FolderController::class, 'show'])
            ->name(RouteNames::FOLDERS_SHOW)
            ->whereNumber('id')
        ;
        Route::get('/info/{id?}', [Controllers\Public\Files\FolderController::class, 'info'])->name(RouteNames::FOLDERS_INFO);

        Route::group(['middleware' => MiddlewareNames::AUTH], static function () {
            Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
                Route::post('/save', [Controllers\Public\Files\FolderController::class, 'save'])->name(RouteNames::FOLDERS_SAVE);
                Route::delete('/delete/{id}', [Controllers\Public\Files\FolderController::class, 'delete'])
                    ->name(RouteNames::FOLDERS_DELETE)
                    ->whereNumber('id')
                ;
            });
        });
    });
});

Route::group(['prefix' => 'files'], static function () {
    Route::get('/{folder?}', [Controllers\Public\Files\FolderController::class, 'index'])->name(RouteNames::FILES);

    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Public\Files\FileController::class, 'list'])->name(RouteNames::FILES_LIST);

        Route::group(['middleware' => MiddlewareNames::VERIFIED], static function () {
            Route::post('/store', [Controllers\Public\Files\FileController::class, 'store'])->name(RouteNames::FILES_STORE);
            Route::post('/save', [Controllers\Public\Files\FileController::class, 'save'])->name(RouteNames::FILES_SAVE);
            Route::post('/replace', [Controllers\Public\Files\FileController::class, 'replace'])->name(RouteNames::FILES_REPLACE);
            Route::post('/up/{id}', [Controllers\Public\Files\FileController::class, 'up'])
                ->name(RouteNames::FILES_UP)
                ->whereNumber('id')
            ;
            Route::post('/down/{id}', [Controllers\Public\Files\FileController::class, 'down'])
                ->name(RouteNames::FILES_DOWN)
                ->whereNumber('id')
            ;
            Route::post('/move', [Controllers\Public\Files\FileController::class, 'move'])->name(RouteNames::FILES_MOVE);
            Route::get('/edit/{id}', [Controllers\Public\Files\FileController::class, 'edit'])
                ->name(RouteNames::FILES_EDIT)
                ->whereNumber('id')
            ;
            Route::delete('/delete/{id}', [Controllers\Public\Files\FileController::class, 'delete'])
                ->name(RouteNames::FILES_DELETE)
                ->whereNumber('id')
            ;
        });
    });
});

Route::get('/storage/{filePath}', [App\Http\Controllers\FileController::class, 'download'])->where('filePath', '.*');
