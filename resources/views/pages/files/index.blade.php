<?php declare(strict_types=1);

use Core\Domains\File\Models\FolderDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\OpenGraph\OpenGraphLocator;

/**
 * @var ?FolderDTO $folder
 */

$openGraph = OpenGraphLocator::OpenGraphFactory()->default();
$openGraph->setUrl(route(RouteNames::FILES));

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical" href="{{ $openGraph->getUrl() }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <h1 class="border-bottom">
        <a href="<?= $openGraph->getUrl() ?>">
            {{ RouteNames::name(Route::current()?->getName()) }}
        </a>
    </h1>
    <folders-block :current-folder='@json($folder)'></folders-block>
@endsection