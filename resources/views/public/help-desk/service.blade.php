<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var TicketTypeEnum $type
 * @var TicketCategory $category
 * @var TicketService  $service
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph
    ->setTitle(RouteNames::name(RouteNames::HELP_DESK) . ' | ' . $type->name() . ' | ' . $category->getName() . ' | ' . $service->getName())
    ->setUrl(route(RouteNames::HELP_DESK_SERVICE, [$type->code(), $category->getCode(), $service->getCode()]))
;

$categories = app(TicketCategoryService::class)->getByType($type);

$userResource    = lc::user()->getId() ? new UserResource(lc::user()) : null;
$accountResource = lc::account()->getId() ? new AccountResource(lc::account()) : null;
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

    <h1 class="page-title">
        <a href="{{ $openGraph->getUrl() }}" class="text-decoration-none">
            <i class="fa {{ $type->icon() }} text-{{ $type->color() }} me-1"></i>
            {{ $type->name() }}
        </a>
    </h1>

    <div class="help-desk">
        <h3 class="my-3 text-success">
            <a href="{{ route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $category->getCode()]) }}"
               class="text-decoration-none">
                <i class="fa fa-folder-open me-2"></i> {{ $category->getName() }} /
            </a>
            <a href="{{ route(RouteNames::HELP_DESK_SERVICE, [$type->code(), $category->getCode(), $service->getCode()]) }}"
               class="text-decoration-none">
                {{ $service->getName() }}
            </a>
        </h3>

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
@endsection
