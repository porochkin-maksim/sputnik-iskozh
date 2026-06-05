<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

include 'web/auth.php';
include 'web/session.php';
include 'web/common.php';
include 'web/home.php';
include 'web/admin.php';
include 'web/public.php';

Route::group(['prefix' => 'pages'], static function () {
    Route::group(['prefix' => 'json'], static function () {
        Route::post('/edit', [Controllers\Public\TemplateController::class, 'get'])->name(RouteNames::TEMPLATE_GET);
        Route::patch('/edit', [Controllers\Public\TemplateController::class, 'update'])->name(RouteNames::TEMPLATE_UPDATE);
    });
});

Route::group(['prefix' => 'webhook'], static function () {
    Route::group(['prefix' => 'acquring'], static function () {
        Route::any('/submit/{acquringId}/{salt}', [Controllers\Webhook\AcquiringController::class, 'submit'])
            ->name(RouteNames::WEBHOOK_ACQURING_SUBMIT)
            ->whereNumber('acquringId')
        ;
        Route::any('/failed/{acquringId}/{salt}', [Controllers\Webhook\AcquiringController::class, 'failed'])
            ->name(RouteNames::WEBHOOK_ACQURING_FAILED)
            ->whereNumber('acquringId')
        ;
    });
});