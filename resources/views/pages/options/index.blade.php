<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::TITLE)
    Отчёты
@endsection

@section(SectionNames::CONTENT)
    <options-block></options-block>
@endsection