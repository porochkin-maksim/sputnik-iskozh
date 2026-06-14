<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_HELP_DESK_INDEX);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, 'Мои заявки')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_HELP_DESK_INDEX) }}

    @if(lc::account()?->getId())
        <div class="page-hero">
            <h3 class="page-hero__title text-dark">
                Мои заявки
            </h3>
            <div class="page-hero__lead">
                История обращений в поддержку СНТ.
            </div>
        </div>

        <help-desk-tickets-block />
    @else
        <hr>
        <h6 class="text-center text-secondary m-0">
            <i>нет заявок...</i>
        </h6>
    @endif
@endsection
