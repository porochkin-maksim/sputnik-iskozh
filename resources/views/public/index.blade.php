<?php declare(strict_types=1);

use Carbon\Carbon;
use Core\Domains\StateSchedule\StateSchedule;
use App\Resources\Views\SectionNames;
use App\Services\Images\StaticFileLocator;
use App\Services\OpenGraph\OpenGraphLocator;

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();

$qrPaymentFile = StaticFileLocator::StaticFileService()->qrPayment();
$periods       = StateSchedule::getScheduledDates(Carbon::now(), 4);
?>

@extends('layouts.app-layout')

@push(SectionNames::META)
    <meta name="keywords"
          content="снт спутник-искож тверь сайт">
@endpush

@section(SectionNames::METRICS)
    @include('layouts.partial.metrics')
@endsection

@section(SectionNames::CONTENT)
    <div class="d-flex justify-content-center border-bottom mb-3">
        <div class="social social-contacts social-index d-flex">
            @include('layouts.partial.social')
        </div>
    </div>
    <index-page
            :qr-payment='@json($qrPaymentFile)'
            :schedule='@json($periods)'
    ></index-page>
    <hr>
    <div>
        @include('public.partial.requests')
    </div>
@endsection