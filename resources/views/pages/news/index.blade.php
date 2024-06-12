<?php declare(strict_types=1);

use Core\Resources\RouteNames;

?>

@extends('layouts.app')

@section('title')
    Новости
@endsection

@section('content')
    <h1>
        <a href="<?= route(RouteNames::NEWS) ?>">Новости</a>
    </h1>
    <news-block></news-block>
@endsection