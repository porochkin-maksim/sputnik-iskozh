<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Verify Your Email Address') }}">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif

        <div class="text-center">
            {{ __('Before proceeding, please check your email for a verification link.') }}
            <br>
            {{ __('If you did not receive the email') }},
                <form class="d-inline"
                      method="POST"
                      action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit"
                        class="btn btn-link p-0 m-0 align-baseline auth-form-actions__link">{{ __('click here to request another') }}</button>
                    .
                </form>
            </div>
    </x-auth.form-card>
@endsection
