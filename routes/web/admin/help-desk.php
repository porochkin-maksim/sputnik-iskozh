<?php declare(strict_types=1);

use App\Http\Controllers\Admin\HelpDesk;
use Core\Domains\HelpDesk\Models\TicketEntity;
use App\Resources\RouteNames;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

Route::prefix('help-desk')->group(function () {
    Route::get('/', static fn() => view('admin.pages.help-desk.index'))->name(RouteNames::ADMIN_HELP_DESK_INDEX);
    Route::get('tickets/view/{id?}', [HelpDesk\TicketController::class, 'view'])->name(RouteNames::ADMIN_HELP_DESK_TICKETS_VIEW)->whereNumber('id');

    Route::prefix('tickets')->name(RouteNames::ADMIN_HELP_DESK . '.tickets.')->group(function () {
        Route::prefix('ajax')->group(function () {
            Route::get('/list', [HelpDesk\TicketController::class, 'list'])->name('list');
            Route::post('/save', [HelpDesk\TicketController::class, 'save'])->name('save');
            Route::delete('/delete/{id}', [HelpDesk\TicketController::class, 'delete'])->name('delete')->whereNumber('id');
        });
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', static fn() => view('admin.pages.help-desk.settings'))->name(RouteNames::ADMIN_HELP_DESK_SETTINGS);

        Route::prefix('ajax')->group(function () {
            Route::prefix('types')->name(RouteNames::ADMIN_HELP_DESK_SETTINGS . '.types.')->group(function () {
                Route::get('/list', [HelpDesk\TicketTypeController::class, 'list'])->name('list');
            });

            Route::prefix('categories')->name(RouteNames::ADMIN_HELP_DESK_SETTINGS . '.categories.')->group(function () {
                Route::get('/list', [HelpDesk\CategoryController::class, 'list'])->name('list');
                Route::get('/create/{type}', [HelpDesk\CategoryController::class, 'create'])->name('create')->whereNumber('type');
                Route::get('/get/{id}', [HelpDesk\CategoryController::class, 'get'])->name('get')->whereNumber('id');
                Route::post('/save', [HelpDesk\CategoryController::class, 'save'])->name('save');
                Route::delete('/delete/{id}', [HelpDesk\CategoryController::class, 'delete'])->name('delete');
            });

            Route::prefix('services')->name(RouteNames::ADMIN_HELP_DESK_SETTINGS . '.services.')->group(function () {
                Route::get('/{categoryId}/list', [HelpDesk\ServiceController::class, 'list'])->name('list')->whereNumber('categoryId');
                Route::get('/create/{categoryId}', [HelpDesk\ServiceController::class, 'create'])->name('create')->whereNumber('categoryId');
                Route::post('/save', [HelpDesk\ServiceController::class, 'save'])->name('save');
                Route::delete('/delete/{id}', [HelpDesk\ServiceController::class, 'delete'])->name('delete');
            });
        });
    });
});

Breadcrumbs::for(RouteNames::ADMIN_HELP_DESK_INDEX, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::ADMIN);
    $trail->push(RouteNames::name(RouteNames::ADMIN_HELP_DESK_INDEX), route(RouteNames::ADMIN_HELP_DESK_INDEX));
});

Breadcrumbs::for(RouteNames::ADMIN_HELP_DESK_TICKETS_VIEW, static function (BreadcrumbTrail $trail, TicketEntity $ticket) {
    $trail->parent(RouteNames::ADMIN_HELP_DESK_INDEX);
    $trail->push(sprintf('№%s', $ticket->getId()), route(RouteNames::ADMIN_HELP_DESK_TICKETS_VIEW, $ticket->getId()));
});

Breadcrumbs::for(RouteNames::ADMIN_HELP_DESK_SETTINGS, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::ADMIN_HELP_DESK_INDEX);
    $trail->push(RouteNames::name(RouteNames::ADMIN_HELP_DESK_SETTINGS), route(RouteNames::ADMIN_HELP_DESK_SETTINGS));
});
