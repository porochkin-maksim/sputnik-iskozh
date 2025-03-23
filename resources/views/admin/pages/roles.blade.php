<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Domains\Access\Enums\PermissionEnum;

?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <roles-block :permissions='@json(PermissionEnum::getCases(true))'></roles-block>
@endsection