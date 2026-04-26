<?php declare(strict_types=1);

use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

$categories         = app(TicketCategoryService::class)->search(new TicketCategorySearcher())->getItems();
$categoriesResource = new CategoryListResource($categories);

$services         = app(TicketCatalogService::class)->search(new TicketServiceSearcher())->getItems();
$servicesResource = new ServiceListResource($services);

$statuses   = new SelectResource(TicketStatusEnum::array());
$priorities = new SelectResource(TicketPriorityEnum::array());
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    <help-desk-tickets-block
            :categories='@json($categoriesResource)'
            :services='@json($servicesResource)'
            :statuses='@json($statuses)'
            :priorities='@json($priorities)'
    ></help-desk-tickets-block>
@endsection
