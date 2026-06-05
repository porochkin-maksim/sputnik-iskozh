<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\Infra\Uid\UidDTO;

/**
 * @var UidDTO $uid
 */
?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="Авторизация по QR-коду">
        <form action="{{ route(RouteNames::LOGING_TOKEN, $uid->getToken()) }}"
              method="POST"
              class="auth-form-stack">
            <div class="auth-form-card__lead alert alert-info">
                Для авторизации на сайте введите пароль,
                который был вам выдан вместе с QR-кодом.
            </div>

            @error('error')
            <div class="text-danger" role="alert">{{ $message }}</div>
            @enderror

            @method('POST')
            @csrf

            <x-auth.form-row for="pin"
                             label="Пароль"
                             :error="$errors->first('pin')">
                <input id="pin"
                       class="form-control text-center"
                       placeholder="введите пароль"
                       name="pin"
                       type="password"
                       autocomplete="current-password">
            </x-auth.form-row>

            <x-auth.form-actions label="Войти"
                                 row-class="mt-2 mb-0"
                                 column-class="d-block" />
        </form>
    </x-auth.form-card>
@endsection
