<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum           $type
 * @var TicketCategory           $category
 * @var TicketService            $service
 * @var TicketCategoryCollection $categories
 * @var null|UserResource        $userResource
 * @var null|AccountResource     $accountResource
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name() . ' | ' . $category->getName() . ' | ' . $service->getName())
    ->setUrl(route(RouteNames::HELP_DESK_SERVICE, [$type->code(), $category->getCode(), $service->getCode()]))
;

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::TITLE)
    {{ $openGraph->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::HELP_DESK_SERVICE, $type, $category, $service) }}

    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'icon' => $type->icon(),
            'iconColor' => $type->color(),
            'text' => $type->name(),
        ])
        <div class="page-hero__lead">
            {{ $category->getName() }} / {{ $service->getName() }}
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <div class="help-desk">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <help-desk-form
                            :account='@json($accountResource)'
                            :user='@json($userResource)'
                            :type='"{{ $type->code() }}"'
                            :category='"{{ $category->getCode() }}"'
                            :service='"{{ $service->getCode() }}"'
                    ></help-desk-form>
                </div>
            </div>
        </div>
    </div>
@endsection
