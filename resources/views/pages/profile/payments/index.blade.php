<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_PAYMENTS_INDEX);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, 'История платежей')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_PAYMENTS_INDEX) }}

    @if(lc::account()?->getId())
        <div class="page-hero">
            <h3 class="page-hero__title text-dark">
                История платежей
            </h3>
            <div class="page-hero__lead">
                Все платежи по всем участкам и периодам.
            </div>
        </div>

        <payments-history-block />
    @else
        <hr>
        <h6 class="text-center text-secondary m-0">
            <i>нет доступа к платежам...</i>
        </h6>
    @endif
@endsection
