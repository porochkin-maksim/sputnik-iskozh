<?php declare(strict_types=1);

use App\Http\Controllers;
use App\Http\Controllers\Admin\System\SentEmailController;
use App\Http\Middleware\Enums\MiddlewareNames;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Route::group(['middleware' => MiddlewareNames::AUTH, 'prefix' => 'admin'], static function () {
    Route::group(['middleware' => MiddlewareNames::ADMIN], static function () {
        Route::get('/', static function () {
            return view('admin.pages.index');
        })->name(RouteNames::ADMIN);

        // главная админки
        Breadcrumbs::for(RouteNames::ADMIN, static function (BreadcrumbTrail $trail) {
            $trail->push('Главная', route(RouteNames::ADMIN));
        });

        // история изменений
        Route::get('/history/changes', Controllers\Admin\HistoryChangesViewController::class)->name(RouteNames::HISTORY_CHANGES);

        Route::group(['prefix' => 'json'], static function () {
            Route::group(['prefix' => 'selects'], static function () {
                Route::get('/accounts', [Controllers\Admin\SelectCollectionsController::class, 'accounts'])->name(RouteNames::ADMIN_SELECTS_ACCOUNTS);
                Route::get('/periods', [Controllers\Admin\SelectCollectionsController::class, 'periods'])->name(RouteNames::ADMIN_SELECTS_PERIODS);
                Route::get('/services-types', [Controllers\Admin\SelectCollectionsController::class, 'servicesTypes'])->name(RouteNames::ADMIN_SELECTS_SERVICES_TYPES);
                Route::get('/counters/{accountId?}', [Controllers\Admin\SelectCollectionsController::class, 'counters'])
                    ->name(RouteNames::ADMIN_SELECTS_COUNTERS)
                    ->whereNumber('id')
                ;
            });
            Route::group(['prefix' => 'top-panel'], static function () {
                Route::get('/', [Controllers\Admin\TopPanelController::class, 'index'])->name(RouteNames::ADMIN_TOP_PANEL_INDEX);
                Route::post('/', [Controllers\Admin\TopPanelController::class, 'search'])->name(RouteNames::ADMIN_TOP_PANEL_SEARCH);
            });
        });

        Route::group(['prefix' => 'qr'], static function () {
            Route::get('view/{uid}', [Controllers\Admin\System\QrCodeController::class, 'view'])->name(RouteNames::ADMIN_QR_VIEW);
        });

        include 'admin/roles.php';
        include 'admin/options.php';
        include 'admin/users.php';
        include 'admin/accounts.php';
        include 'admin/counters.php';
        include 'admin/billing.php';
        include 'admin/help-desk.php';

        // просмотр истории отправки писем
        Route::resource('emails', SentEmailController::class)
            ->only(['index', 'show', 'destroy'])
            ->names([
                'index'   => 'admin.emails.index',
                'show'    => 'admin.emails.show',
                'destroy' => 'admin.emails.destroy',
            ])
        ;

        // просмотр ошибок
        Route::get('error-logs', [Controllers\Admin\System\ErrorLogsController::class, 'index'])->name('admin.error-logs.index');
        Route::get('error-logs/{filename}', [Controllers\Admin\System\ErrorLogsController::class, 'show'])->name('admin.error-logs.show');
        Route::get('error-logs/{filename}/details/{index}', [Controllers\Admin\System\ErrorLogsController::class, 'details'])->name('admin.error-logs.details');

        // Управление очередями
        Route::group(['prefix' => 'queue'], static function () {
            Route::get('/', [Controllers\Admin\QueueController::class, 'index'])->name(RouteNames::ADMIN_QUEUE);
            Route::get('/status', [Controllers\Admin\QueueController::class, 'status'])->name(RouteNames::ADMIN_QUEUE_STATUS);
            Route::post('/start', [Controllers\Admin\QueueController::class, 'start'])->name(RouteNames::ADMIN_QUEUE_START);
            Route::post('/stop', [Controllers\Admin\QueueController::class, 'stop'])->name(RouteNames::ADMIN_QUEUE_STOP);
            Route::post('/clear', [Controllers\Admin\QueueController::class, 'clear'])->name(RouteNames::ADMIN_QUEUE_CLEAR);
        });
    });
});
