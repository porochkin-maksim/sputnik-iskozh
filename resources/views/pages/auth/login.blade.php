<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Login') }}">
        <form method="POST" action="{{ route('login') }}" class="auth-form-stack">
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
                       autocomplete="email"
                       autofocus>
            </x-auth.form-row>

            <x-auth.form-row for="password"
                             :label="__('Password')"
                             :error="$errors->first('password')">
                <input id="password"
                       type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       name="password"
                       required
                       autocomplete="current-password">
            </x-auth.form-row>

            <x-auth.form-row row-class="row g-2 align-items-center auth-form-row auth-form-row--remember"
                             input-class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input id="remember"
                           type="checkbox"
                           class="form-check-input"
                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </x-auth.form-row>

            <x-auth.form-actions :label="__('Login')">
                @if (Route::has('password.request'))
                    <a class="auth-form-actions__link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </x-auth.form-actions>
        </form>
    </x-auth.form-card>
@endsection
