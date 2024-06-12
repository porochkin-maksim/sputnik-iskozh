<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

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
