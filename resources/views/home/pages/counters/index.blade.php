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
    <counters-block></counters-block>
@endsection