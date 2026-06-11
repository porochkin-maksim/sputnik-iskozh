<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Reset Password') }}">
        <reset-password token="{{ $token }}" email="{{ $email ?? '' }}"></reset-password>
    </x-auth.form-card>
@endsection
