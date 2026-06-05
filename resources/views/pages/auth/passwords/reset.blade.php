<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Reset Password') }}">
        <form method="POST" action="{{ route('password.update') }}" class="auth-form-stack">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

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

            <x-auth.form-actions :label="__('Reset Password')" />
        </form>
    </x-auth.form-card>
@endsection
