<?php declare(strict_types=1);

use Core\Domains\File\Models\FolderDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var ?FolderDTO $folder
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Файлы и документация');
$openGraph->setUrl(route(RouteNames::FILES));

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <folders-block :current-folder='@json($folder)'></folders-block>
@endsection