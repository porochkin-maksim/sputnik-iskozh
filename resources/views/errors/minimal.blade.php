<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends($layout ?? 'layouts.app-layout')

@section(SectionNames::CONTENT)
    <div class="errors">
        <div class="errors-inner">
            <div class="error-code">
                @yield('code')
            </div>
            <div class="error-message">
                @yield('message')
            </div>
            <a href="/" class="error-back-link">
                <i class="fa fa-arrow-left"></i>
                На главную
            </a>
        </div>
    </div>
@endsection
