<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Reset Password') }}">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="auth-form-stack">
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

            <x-auth.form-actions :label="__('Send Password Reset Link')" />
        </form>
    </x-auth.form-card>
@endsection
