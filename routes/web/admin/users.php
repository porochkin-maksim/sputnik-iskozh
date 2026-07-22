<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Resources\RouteNames;
use Core\Domains\User\UserEntity;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Route::group(['prefix' => 'users'], static function () {
    Route::get('/', [Controllers\Admin\System\UsersController::class, 'index'])->name(RouteNames::ADMIN_USER_INDEX);
    Route::get('/view/{id?}', [Controllers\Admin\System\UsersController::class, 'view'])->name(RouteNames::ADMIN_USER_VIEW)
        ->whereNumber('id')
    ;

    Breadcrumbs::for(RouteNames::ADMIN_USER_INDEX, static function (BreadcrumbTrail $trail) {
        $previousUrl = url()->previous();
        $routeName   = Route::getRoutes()->match(Request::create($previousUrl))->getName();
        $route       = route(RouteNames::ADMIN_USER_INDEX);
        if ($routeName === RouteNames::ADMIN_USER_INDEX) {
            $route = $previousUrl;
        }

        $trail->parent(RouteNames::ADMIN);
        $trail->push(RouteNames::name(RouteNames::ADMIN_USER_INDEX), $route);
    });

    Breadcrumbs::for(RouteNames::ADMIN_USER_VIEW, static function (BreadcrumbTrail $trail, UserEntity $user) {
        $trail->parent(RouteNames::ADMIN_USER_INDEX);
        $trail->push('Пользователь №' . $user->getId(), route(RouteNames::ADMIN_USER_VIEW, $user->getId()));
    });

    Route::get('/json/get/{id?}', [Controllers\Admin\System\UsersController::class, 'get'])->name(RouteNames::ADMIN_USER_GET)
        ->whereNumber('id')
    ;
    Route::get('/export', [Controllers\Admin\System\UsersController::class, 'export'])->name(RouteNames::ADMIN_USER_EXPORT);

    Route::group(['prefix' => 'import'], static function () {
        Route::get('/', [Controllers\Admin\System\UsersImportController::class, 'index'])->name(RouteNames::ADMIN_USER_IMPORT_INDEX);
        Route::post('/parse-file', [Controllers\Admin\System\UsersImportController::class, 'parseFile'])->name(RouteNames::ADMIN_USER_IMPORT_PARSE_FILE);
        Route::post('/save', [Controllers\Admin\System\UsersImportController::class, 'save'])->name(RouteNames::ADMIN_USER_IMPORT_SAVE);

        Breadcrumbs::for(RouteNames::ADMIN_USER_IMPORT_INDEX, static function (BreadcrumbTrail $trail) {
            $trail->parent(RouteNames::ADMIN_USER_INDEX);
            $trail->push(RouteNames::name(RouteNames::ADMIN_USER_IMPORT_INDEX), route(RouteNames::ADMIN_USER_IMPORT_INDEX));
        });
    });

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
        Route::post('/qr/send-login-link/{userId}', [Controllers\Admin\System\QrCodeController::class, 'makeLoginLinkAndSendEmail'])
            ->name(RouteNames::ADMIN_LOGIN_LINK_SEND)
            ->whereNumber('userId')
        ;
    });
});
