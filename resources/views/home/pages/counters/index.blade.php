<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_COUNTERS);
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_COUNTERS) }}
    <profile-counters-block></profile-counters-block>
@endsection