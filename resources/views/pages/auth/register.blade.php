<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Register') }}">
        <form method="POST" action="{{ route('register') }}" class="auth-form-stack">
            @csrf

            <x-auth.form-row for="email"
                             :label="__('Email Address')"
                             :error="$errors->first('email')">
                <input id="email"
                       type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email">
            </x-auth.form-row>

            <x-auth.form-row for="password"
                             :label="__('Password')"
                             :error="$errors->first('password')">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="new-password">
            </x-auth.form-row>

            <x-auth.form-row for="password-confirm"
                             :label="__('Confirm Password')">
                <input id="password-confirm"
                       type="password"
                       class="form-control"
                       name="password_confirmation"
                       required
                       autocomplete="new-password">
            </x-auth.form-row>

            <x-auth.form-actions :label="__('Register')" />
        </form>
    </x-auth.form-card>
@endsection
