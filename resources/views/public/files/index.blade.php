<?php declare(strict_types=1);

use Core\Domains\Folders\Models\FolderDTO;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var ?FolderDTO $folder
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setDescription('Файлы и документация');
$openGraph->setUrl(route(RouteNames::FILES));

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::FILES) }}
    <h1 class="page-title">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <folders-block :current-folder='@json($folder)'></folders-block>
@endsection