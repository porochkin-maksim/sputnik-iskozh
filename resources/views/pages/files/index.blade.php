<?php declare(strict_types=1); ?>

@extends('layouts.app')

@section('title')
    Файлы
@endsection

@section('content')
    <h1>
        <a href="<?= route(\Core\Resources\RouteNames::FILES) ?>">Файлы</a>
    </h1>
    <files-block></files-block>
@endsection