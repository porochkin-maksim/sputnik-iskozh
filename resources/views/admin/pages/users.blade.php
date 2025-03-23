<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Domains\Access\Enums\PermissionEnum;

?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <users-block></users-block>
@endsection