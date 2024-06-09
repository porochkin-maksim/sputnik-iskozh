<?php declare(strict_types=1); ?>

@extends('layouts.app')

@section('title')
    Отчёты
@endsection

@section('content')
    <h1>
        <a href="<?= route(\Core\Resources\RouteNames::REPORTS) ?>">Отчёты</a>
    </h1>
    <reports-block></reports-block>
@endsection