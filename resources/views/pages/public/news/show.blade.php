<?php declare(strict_types=1);

use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Shared\Files\FileResource;
use App\Locators\NewsLocator;
use Core\Domains\News\NewsEntity;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use App\Services\OpenGraph\Enums\OpenGraphType;
use App\Services\OpenGraph\OpenGraphLocator;

/**
 * @var NewsEntity $news
 * @var bool       $edit
 */
$newsResource = new NewsResource($news);
$firstImage   = $news->getImages()?->first();

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setType(OpenGraphType::ARTICLE)
    ->setTitle($news->getTitle())
    ->setUrl($newsResource->getUrl())
    ->setImage($firstImage ? (new FileResource($firstImage))->getUrl() : null)
    ->setDescription($news->getDescription() ? : $news->getArticleAsText())
;
?>

@extends('layouts.app-layout')

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::TITLE)
    {{ $news->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::NEWS_SHOW, $news) }}
    <div class="page-hero">
        @include('layouts.partial.page-title', [
            'href' => $openGraph->getUrl(),
            'text' => $news->getTitle(),
        ])
        <div class="page-hero__lead">
            {{ $news->getDescription() ?: 'Новость и подробности публикации.' }}
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4 public-article-card">
        <news-show
            :news='@json($newsResource)'
        ></news-show>
    </div>
@endsection
