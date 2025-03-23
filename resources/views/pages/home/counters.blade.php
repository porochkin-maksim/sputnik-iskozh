<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    <profile-counters-block></profile-counters-block>
@endsection