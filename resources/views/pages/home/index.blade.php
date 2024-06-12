<?php declare(strict_types=1);

use Core\Objects\Account\Models\AccountDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var AccountDTO $account
 */
?>

@extends(ViewNames::LAYOUTS_TWO_COLUMN)

@section(SectionNames::TITLE)
    @relativeInclude('partial.title')
@endsection

@section(SectionNames::SUB)
    @relativeInclude('partial.nav')
@endsection

@section(SectionNames::MAIN)

@endsection