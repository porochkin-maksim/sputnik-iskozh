<?php declare(strict_types=1);

use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();

$qrPaymentFile = StaticFileLocator::StaticFileService()->qrPayment();
?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <meta name="keywords"
          content="снт спутник-искож тверь сайт">
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::PARTIAL_METRICS)
@endsection

@section(SectionNames::CONTENT)
    <div class="d-flex justify-content-center border-bottom mb-3">
        <div class="social social-contacts social-index d-flex">
            @include(ViewNames::PARTIAL_SOCIAL)
        </div>
    </div>
    <index-page :qr-payment='@json($qrPaymentFile)'></index-page>
    <hr>
    @include(ViewNames::PARTIAL_REQUESTS)
@endsection