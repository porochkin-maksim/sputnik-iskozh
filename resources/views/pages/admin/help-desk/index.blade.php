<?php declare(strict_types=1);

use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

/** @var TicketCategoryCollection $categories */
$categoriesResource = new CategoryListResource($categories);

/** @var TicketServiceCollection $services */
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
