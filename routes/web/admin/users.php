<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Resources\RouteNames;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users'], static function () {
    Route::get('/', [Controllers\Admin\System\UsersController::class, 'index'])->name(RouteNames::ADMIN_USER_INDEX);
    Route::get('/view/{id?}', [Controllers\Admin\System\UsersController::class, 'view'])->name(RouteNames::ADMIN_USER_VIEW)
        ->whereNumber('id')
    ;
    Route::get('/export', [Controllers\Admin\System\UsersController::class, 'export'])->name(RouteNames::ADMIN_USER_EXPORT);
    Route::group(['prefix' => 'json'], static function () {
        Route::get('/list', [Controllers\Admin\System\UsersController::class, 'list'])->name(RouteNames::ADMIN_USER_LIST);
        Route::post('/save', [Controllers\Admin\System\UsersController::class, 'save'])->name(RouteNames::ADMIN_USER_SAVE);
        Route::post('/generate-email', [Controllers\Admin\System\UsersController::class, 'generateEmail'])->name(RouteNames::ADMIN_USER_GENERATE_EMAIL);
        Route::delete('/{id}', [Controllers\Admin\System\UsersController::class, 'delete'])
            ->name(RouteNames::ADMIN_USER_DELETE)
            ->whereNumber('id')
        ;
        Route::patch('/{id}', [Controllers\Admin\System\UsersController::class, 'restore'])
            ->name(RouteNames::ADMIN_USER_RESTORE)
            ->whereNumber('id')
        ;
        Route::post('/sendRestorePassword', [Controllers\Admin\System\UsersController::class, 'sendRestorePassword'])->name(RouteNames::ADMIN_USER_SEND_RESTORE_PASSWORD);
        Route::post('/send-invite-password', [Controllers\Admin\System\UsersController::class, 'sendInviteWithPassword'])->name(RouteNames::ADMIN_USER_SEND_INVITE_WITH_PASSWORD);
        Route::post('/qr/login/{userId}/{pin}', [Controllers\Admin\System\QrCodeController::class, 'makeLoginLink'])
            ->name(RouteNames::ADMIN_LOGIN_LINK)
            ->whereNumber('userId')
        ;
    });
});
