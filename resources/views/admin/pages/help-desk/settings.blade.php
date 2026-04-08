<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_HELP_DESK_SETTINGS) }}
    <help-desk-ticket-category-block></help-desk-ticket-category-block>
@endsection