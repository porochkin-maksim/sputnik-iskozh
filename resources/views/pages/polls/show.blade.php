<?php declare(strict_types=1);

use Core\Domains\Poll\Models\PollDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\Enums\OpenGraphType;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var PollDTO $poll
 * @var bool    $edit
 */
$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setType(OpenGraphType::ARTICLE)
    ->setTitle($poll->getTitle())
    ->setDescription($poll->getDescription());

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical"
          href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::TITLE)
    {{ $poll->getTitle() }}
@endsection

@section(SectionNames::CONTENT)
    <poll-show :poll='@json($poll)'
               :edit='@json($edit)'
    ></poll-show>
@endsection