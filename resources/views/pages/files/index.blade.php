<?php declare(strict_types=1);

use Core\Domains\File\Models\FolderDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var ?FolderDTO $folder
 */
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::TITLE)
    Файлы
@endsection

@section(SectionNames::CONTENT)
    <h1>
        <a href="<?= route(RouteNames::FILES) ?>">Файлы</a>
    </h1>
    <folders-block :current-folder='@json($folder)'
    ></folders-block>
@endsection