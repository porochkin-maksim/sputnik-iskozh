<?php declare(strict_types=1);

use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

$categories         = HelpDeskServiceLocator::TicketCategoryService()->search(new TicketCategorySearcher())->getItems();
$categoriesResource = new CategoryListResource($categories);

$services         = HelpDeskServiceLocator::TicketServiceService()->search(new TicketServiceSearcher())->getItems();
$servicesResource = new ServiceListResource($services);

$statuses   = new SelectResource(TicketStatusEnum::array());
$priorities = new SelectResource(TicketPriorityEnum::array());
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <help-desk-tickets-block
            :categories='@json($categoriesResource)'
            :services='@json($servicesResource)'
            :statuses='@json($statuses)'
            :priorities='@json($priorities)'
    ></help-desk-tickets-block>
@endsection