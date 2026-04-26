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
    ->setUrl(NewsLocator::UrlFactory()->makeUrl($news))
    ->setImage($firstImage ? new FileResource($firstImage)->getUrl() : null)
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
    <news-show :news='@json($newsResource)'
               :edit='@json($edit)'
    ></news-show>
@endsection