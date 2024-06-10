<?php declare(strict_types=1);

use Core\Objects\News\Models\NewsDTO;

/**
 * @var NewsDTO $news
 * @var bool    $edit
 */
?>

@extends('layouts.app')

@section('title')
    {{ $news->getTitle() }}
@endsection

@section('content')
    <news-item :news='<?= json_encode($news) ?>'
               :edit='<?= json_encode($edit) ?>'
    ></news-item>
@endsection