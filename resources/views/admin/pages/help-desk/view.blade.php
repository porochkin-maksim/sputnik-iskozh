<?php declare(strict_types=1);

use App\Http\Resources\Admin\Accounts\AccountsListResource2;
use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use App\Http\Resources\Admin\Users\UsersListResource2;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\Account\AccountLocator;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Domains\User\UserLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var TicketDTO $ticket ;
 */

$ticketResource = new TicketResource($ticket);

$categories         = HelpDeskServiceLocator::TicketCategoryService()->search()->getItems();
$categoriesResource = new CategoryListResource($categories);

$services         = HelpDeskServiceLocator::TicketServiceService()->search()->getItems();
$servicesResource = new ServiceListResource($services);

$types      = new SelectResource(TicketTypeEnum::array());
$statuses   = new SelectResource(TicketStatusEnum::array());
$priorities = new SelectResource(TicketPriorityEnum::array());

$accounts = AccountLocator::AccountService()->search()->getItems();
$accounts = new AccountsListResource2($accounts);

$users = UserLocator::UserService()->search()->getItems();
$users = new UsersListResource2($users);
?>

@extends(ViewNames::LAYOUTS_ADMIN)

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