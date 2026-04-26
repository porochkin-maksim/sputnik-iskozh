<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Counter\CounterEntity;
use App\Resources\RouteNames;
use Illuminate\Support\Facades\Route;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Route::group(['prefix' => 'accounts'], static function () {
    Route::get('/', [Controllers\Admin\Account\AccountsController::class, 'index'])->name(RouteNames::ADMIN_ACCOUNT_INDEX);

    Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_INDEX, static function (BreadcrumbTrail $trail) {
        $trail->push('Участки', route(RouteNames::ADMIN_ACCOUNT_INDEX));
    });

    Route::group(['prefix' => 'json'], static function () {
        Route::get('/view/{accountId}', [Controllers\Admin\Account\AccountsController::class, 'get'])
            ->name(RouteNames::ADMIN_ACCOUNT_GET)
            ->whereNumber('accountId')
        ;
        Route::get('/create', [Controllers\Admin\Account\AccountsController::class, 'create'])->name(RouteNames::ADMIN_ACCOUNT_CREATE);
        Route::get('/list', [Controllers\Admin\Account\AccountsController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_LIST);
        Route::post('/save', [Controllers\Admin\Account\AccountsController::class, 'save'])->name(RouteNames::ADMIN_ACCOUNT_SAVE);
        Route::delete('/{id}', [Controllers\Admin\Account\AccountsController::class, 'delete'])
            ->name(RouteNames::ADMIN_ACCOUNT_DELETE)
            ->whereNumber('id')
        ;
    });
    Route::group(['prefix' => 'view/{accountId}', 'where' => ['accountId' => '[0-9]+']], static function () {
        Route::get('/', [Controllers\Admin\Account\AccountsController::class, 'view'])->name(RouteNames::ADMIN_ACCOUNT_VIEW);

        Breadcrumbs::for(RouteNames::ADMIN_ACCOUNT_VIEW, static function (BreadcrumbTrail $trail, AccountEntity $account) {
            $trail->parent(RouteNames::ADMIN_ACCOUNT_INDEX);
            $trail->push('Участок №' . $account->getNumber(), route(RouteNames::ADMIN_ACCOUNT_VIEW, $account->getId()));
        });

        Route::group(['prefix' => 'counters'], static function () {
            Route::get('/view/{counterId}', [Controllers\Admin\Account\CounterController::class, 'view'])
                ->name(RouteNames::ADMIN_COUNTER_VIEW)
                ->whereNumber('counterId')
            ;

            Breadcrumbs::for(RouteNames::ADMIN_COUNTER_VIEW, static function (BreadcrumbTrail $trail, CounterEntity $counter) {
                $trail->parent(RouteNames::ADMIN_ACCOUNT_VIEW, $counter->getAccount());
                $trail->push('Счётчик №' . $counter->getNumber(), route(RouteNames::ADMIN_COUNTER_VIEW, [$counter->getAccountId(), $counter->getId()]));
            });

            Route::group(['prefix' => 'json'], static function () {
                Route::post('/create', [Controllers\Admin\Account\CounterController::class, 'create'])->name(RouteNames::ADMIN_COUNTER_CREATE);
                Route::get('/list', [Controllers\Admin\Account\CounterController::class, 'list'])->name(RouteNames::ADMIN_COUNTER_LIST);
                Route::post('/save', [Controllers\Admin\Account\CounterController::class, 'save'])->name(RouteNames::ADMIN_COUNTER_SAVE);
                Route::post('/delete/{counterId}', [Controllers\Admin\Account\CounterController::class, 'delete'])
                    ->name(RouteNames::ADMIN_COUNTER_DELETE)
                    ->whereNumber('counterId')
                ;
                Route::post('/add-value', [Controllers\Admin\Account\CounterController::class, 'addValue'])->name(RouteNames::ADMIN_COUNTER_ADD_VALUE);
            });
        });
        Route::group(['prefix' => 'invoices'], static function () {
            Route::group(['prefix' => 'json'], static function () {
                Route::get('/list', [Controllers\Admin\Account\InvoiceController::class, 'list'])->name(RouteNames::ADMIN_ACCOUNT_INVOICE_LIST);
            });
        });
    })->whereNumber('accountId');
});
