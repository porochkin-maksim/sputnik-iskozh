<?php declare(strict_types=1); ?>

@extends('layouts.app')

@section('title')
    Новости
@endsection

@section('content')
    <h1>
        <a href="<?= route(\Core\Resources\RouteNames::NEWS) ?>">Новости</a>
    </h1>
    <news-block></news-block>
@endsection