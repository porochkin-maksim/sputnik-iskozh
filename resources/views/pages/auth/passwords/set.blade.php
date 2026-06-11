<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="Установить пароль">
        <set-password token="{{ $token }}" email="{{ $email ?? '' }}"></set-password>
    </x-auth.form-card>
@endsection
