<?php declare(strict_types=1);

use App\Http\Resources\Shared\Files\FolderResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var ?FolderResource $folder
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
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => RouteNames::name(Route::current()?->getName()),
        ])
        <div class="page-hero__lead">
            Файлы, формы, документы и другие материалы для скачивания.
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <folders-block :current-folder='@json($folder)'></folders-block>
    </div>
@endsection
