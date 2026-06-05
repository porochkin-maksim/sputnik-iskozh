<?php declare(strict_types=1);

use App\Http\Resources\Public\NewsResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var null|NewsResource                    $newsResource
 * @var list<array{key: int, value: string}> $categories
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::NEWS));

$title = $newsResource ? 'Редактировать новость' : 'Добавить новость';

?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::NEWS_FORM, $newsResource?->getEntity()) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => $title,
        ])
        <div class="page-hero__lead">
            {{ $newsResource ? 'Редактирование публикации' : 'Создание новой публикации' }}
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <news-item-edit
                :model-value='@json($newsResource)'
                :categories='@json($categories)'
        ></news-item-edit>
    </div>
@endsection