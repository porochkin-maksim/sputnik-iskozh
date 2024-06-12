<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <div class="layout-page">
        <div class="block-sub">
            @yield(SectionNames::SUB)
        </div>
        <div class="block-main">
            @yield(SectionNames::MAIN)
        </div>
    </div>
@endsection