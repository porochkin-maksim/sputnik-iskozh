<?php declare(strict_types=1);

use App\Http\Controllers;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Resources\RouteNames;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'help-desk'], static function () {
    Route::get('/', [Controllers\Public\HelpDesk\HelpDeskController::class, 'index'])->name(RouteNames::HELP_DESK);
    Route::get('/{type}', [Controllers\Public\HelpDesk\HelpDeskController::class, 'type'])->name(RouteNames::HELP_DESK_TYPE);
    Route::get('/{type}/{category}', [Controllers\Public\HelpDesk\HelpDeskController::class, 'category'])->name(RouteNames::HELP_DESK_CATEGORY);
    Route::get('/{type}/{category}/{service}', [Controllers\Public\HelpDesk\HelpDeskController::class, 'form'])->name(RouteNames::HELP_DESK_SERVICE);

    Route::post('/{type}/{category}/{service}', [Controllers\Public\HelpDesk\HelpDeskController::class, 'ticket'])->name(RouteNames::HELP_DESK_TICKET);
});

Breadcrumbs::for(RouteNames::HELP_DESK, static function (BreadcrumbTrail $trail) {
    $trail->parent(RouteNames::REQUESTS);
    $trail->push(RouteNames::name(RouteNames::HELP_DESK), route(RouteNames::HELP_DESK));
});
Breadcrumbs::for(RouteNames::HELP_DESK_TYPE, static function (BreadcrumbTrail $trail, TicketTypeEnum $type) {
    $trail->parent(RouteNames::HELP_DESK);
    $trail->push($type->name(), route(RouteNames::HELP_DESK_TYPE, $type->code()));
});
Breadcrumbs::for(RouteNames::HELP_DESK_CATEGORY, static function (BreadcrumbTrail $trail, TicketTypeEnum $type, TicketCategoryDTO $category) {
    $trail->parent(RouteNames::HELP_DESK_TYPE, $type);
    $trail->push($category->getName(), route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $category->getCode()]));
});
Breadcrumbs::for(RouteNames::HELP_DESK_SERVICE, static function (BreadcrumbTrail $trail, TicketTypeEnum $type, TicketCategoryDTO $category, TicketServiceDTO $service) {
    $trail->parent(RouteNames::HELP_DESK_CATEGORY, $type, $category);
    $trail->push($service->getName(), route(RouteNames::HELP_DESK_SERVICE, [$type->code(), $category->getCode(), $service->getCode()]));
});