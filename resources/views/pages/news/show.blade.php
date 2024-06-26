<?php declare(strict_types=1);

use Core\Domains\News\Models\NewsDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var NewsDTO $news
 * @var bool    $edit
 */
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::TITLE)
    {{ $news->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    <news-show :news='@json($news)'
               :edit='@json($edit)'
    ></news-show>
@endsection