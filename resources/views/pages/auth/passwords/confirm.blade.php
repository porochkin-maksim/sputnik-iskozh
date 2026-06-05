<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Confirm Password') }}">
        {{ __('Please confirm your password before continuing.') }}

        <form method="POST" action="{{ route('password.confirm') }}" class="auth-form-stack">
            @csrf

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

            <x-auth.form-actions :label="__('Confirm Password')"
                                 column-class="col-12 col-lg-8 offset-lg-4">
                @if (Route::has('password.request'))
                    <a class="auth-form-actions__link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </x-auth.form-actions>
        </form>
    </x-auth.form-card>
@endsection
