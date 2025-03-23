<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <counter-history-block></counter-history-block>
@endsection