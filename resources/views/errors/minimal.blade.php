<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <div class="errors">
        <div class="d-flex flex-column justify-content-center align-items-center w-100">
            <div class="error-code">
                @yield('code')
            </div>
            <div class="error-message">
                @yield('message')
            </div>
        </div>
    </div>
@endsection
