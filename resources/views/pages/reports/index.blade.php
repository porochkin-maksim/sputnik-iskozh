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
    <h1>
        <a href="<?= route(RouteNames::REPORTS) ?>">Отчёты</a>
    </h1>
    <reports-block></reports-block>
@endsection