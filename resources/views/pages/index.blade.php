<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Illuminate\Support\Facades\URL;

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical" href="{{ URL::current() }}" />
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <index-page></index-page>
@endsection