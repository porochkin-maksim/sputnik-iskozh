<?php declare(strict_types=1);

use Core\Objects\News\Models\NewsDTO;
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
    <news-item :news='<?= json_encode($news) ?>'
               :edit='<?= json_encode($edit) ?>'
    ></news-item>
@endsection