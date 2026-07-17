<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;

/**
 * @var PeriodCollection  $periods
 * @var null|PeriodEntity $period
 */
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_INVOICES, $period);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_INVOICES, $period) }}

    <profile-invoices-block csrf-token='{{ csrf_token() }}'></profile-invoices-block>

    @if($periods->count() && $period)
        <div class="mt-4">
            <h3 class="text-dark text-center">Статистика СНТ в периоде «{{ $period->getName() }}»</h3>
            <summary-block :period-id="{{ $period->getId() }}"
                           :show-invoice="false"></summary-block>
        </div>
    @endif
@endsection
