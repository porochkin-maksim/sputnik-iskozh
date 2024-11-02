<?php declare(strict_types=1);

use Core\Domains\News\Models\NewsDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\Enums\OpenGraphType;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var NewsDTO $news
 * @var bool    $edit
 */
$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setType(OpenGraphType::ARTICLE)
    ->setTitle($news->getTitle())
    ->setUrl($news->url())
    ->setImage($news->getImages()->first()?->url())
    ->setDescription($news->getDescription() ?: $news->getArticleAsText());

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::TITLE)
    {{ $news->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    <news-show :news='@json($news)'
               :edit='@json($edit)'
    ></news-show>
@endsection