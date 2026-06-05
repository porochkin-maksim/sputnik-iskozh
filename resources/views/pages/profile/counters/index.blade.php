<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_COUNTERS);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_COUNTERS) }}
    <div class="page-hero">
        <h3 class="page-hero__title text-dark mb-0">Показания счётчиков</h3>
        <div class="page-hero__lead">
            Просмотр истории показаний и добавление новых данных по вашему участку.
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <counters-block></counters-block>
    </div>
@endsection
