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
    <div class="page-hero text-center">
        @include('partials.public.social-strip', ['index' => true, 'class' => 'mb-0'])
        <div class="mt-3">
            <h1 class="page-hero__title mb-2">{{ config('app.name') }}</h1>
            <div class="page-hero__lead">
                Информация, заявления, документы и сервисы для жителей СНТ.
            </div>
        </div>
    </div>

    <div class="page-section page-card p-3 p-lg-4">
        <index-page
                :qr-payment='@json($qrPaymentFile)'
                :schedule='@json($periods)'
        ></index-page>
    </div>

    <div class="page-section">
        @include('partials.public.requests-section', ['class' => 'mt-0'])
    </div>
@endsection
