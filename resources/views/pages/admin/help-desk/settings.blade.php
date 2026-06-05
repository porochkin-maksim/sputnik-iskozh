<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_HELP_DESK_SETTINGS) }}
    <help-desk-ticket-category-block></help-desk-ticket-category-block>
@endsection