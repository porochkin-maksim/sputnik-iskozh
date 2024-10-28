<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <profile-block :account='@json(\app::account())'
                   :user='@json(\app::user())'
    ></profile-block>
@endsection

@section(SectionNames::SUB)

@endsection