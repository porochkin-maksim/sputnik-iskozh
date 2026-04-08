<?php declare(strict_types=1);

use Core\Domains\Infra\Uid\UidDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var UidDTO $uid
 */
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <div class="d-flex justify-content-center align-items-center pt-3 text-center">
        <form action="{{ route(RouteNames::LOGING_TOKEN, $uid->getToken()) }}"
              method="POST">
            <div class="alert alert-info mb-2">
                Для авторизации на сайте введите пароль,<br> который был вам выдан вместе с QR-кодом
            </div>

            @error('error')
                <div class="text-danger mb-2" role="alert">{{ $message }}</div>
            @enderror

            <div>
                @method('POST')
                @csrf
                <div>
                    <input class="form-control text-center"
                           placeholder="введите пароль"
                           name="pin">
                </div>

                <div class="mt-2">
                    <button type="submit"
                            class="btn btn-success">Войти
                    </button>
                </div>
            </div>
        </form>
        <div>
@endsection