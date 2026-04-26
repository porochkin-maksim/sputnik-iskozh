<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    <accounts-block></accounts-block>
@endsection