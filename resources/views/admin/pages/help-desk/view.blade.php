<?php declare(strict_types=1);

use App\Http\Resources\Admin\Accounts\AccountsListResource2;
use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use App\Http\Resources\Admin\Users\UsersListResource2;
use App\Http\Resources\Common\SelectResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\User\UserService;

/**
 * @var TicketEntity $ticket ;
 */

$ticketResource = new TicketResource($ticket);

$categories         = app(TicketCategoryService::class)->search()->getItems();
$categoriesResource = new CategoryListResource($categories);

$services         = app(TicketCatalogService::class)->search()->getItems();
$servicesResource = new ServiceListResource($services);

$types      = new SelectResource(TicketTypeEnum::array());
$statuses   = new SelectResource(TicketStatusEnum::array());
$priorities = new SelectResource(TicketPriorityEnum::array());

$accounts = app(AccountService::class)->search()->getItems();
$accounts = new AccountsListResource2($accounts);

$users = app(UserService::class)->search()->getItems();
$users = new UsersListResource2($users);
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_HELP_DESK_TICKETS_VIEW, $ticket) }}
    <help-desk-ticket-view
            :ticket='@json($ticketResource)'
            :users='@json($users)'
            :accounts='@json($accounts)'
            :categories='@json($categoriesResource)'
            :services='@json($servicesResource)'
            :types='@json($types)'
            :statuses='@json($statuses)'
            :priorities='@json($priorities)'
    ></help-desk-ticket-view>
@endsection
