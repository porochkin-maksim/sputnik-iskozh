<?php declare(strict_types=1);

use App\Http\Resources\Admin\AccountResource;
use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use App\Http\Resources\Admin\Users\UsersListResource2;
use App\Http\Resources\Common\SelectResource;
use App\Http\Resources\Shared\ResourseList;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\Account\AccountCollection;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\User\UserCollection;

/**
 * @var TicketEntity             $ticket
 * @var TicketCategoryCollection $categories
 * @var TicketServiceCollection  $services
 * @var AccountCollection        $accounts
 * @var UserCollection           $users
 */

$ticketResource = new TicketResource($ticket);

$categoriesResource = new CategoryListResource($categories);

$servicesResource = new ServiceListResource($services);

$types      = new SelectResource(TicketTypeEnum::array());
$statuses   = new SelectResource(TicketStatusEnum::array());
$priorities = new SelectResource(TicketPriorityEnum::array());

$accounts = new ResourseList($accounts, AccountResource::class);

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
