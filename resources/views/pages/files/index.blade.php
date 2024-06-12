<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::TITLE)
    Файлы
@endsection

@section(SectionNames::CONTENT)
    <h1>
        <a href="<?= route(RouteNames::FILES) ?>">Файлы</a>
    </h1>
    <files-block></files-block>
@endsection