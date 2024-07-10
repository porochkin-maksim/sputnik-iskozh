<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\StaticFileLocator;
use Core\Services\OpenGraph\Enums\OpenGraphType;
use Core\Services\OpenGraph\Models\OpenGraph;
use Illuminate\Support\Facades\Route;

$openGraph = new OpenGraph();
$openGraph->setType(OpenGraphType::WEBSITE)
    ->setTitle(RouteNames::name(Route::current()?->getName(), env('APP_NAME')))
    ->setUrl(route(RouteNames::INDEX))
    ->setImage(StaticFileLocator::StaticFileService()->logoSnt()->getUrl())
    ->setDescription('Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь');

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <meta name="keywords"
          content="снт спутник-искож тверь сайт">
    <link rel="canonical"
          href="{{ route(RouteNames::INDEX) }}" />
    {!! $openGraph->toMetaTags() !!}
@endpush

@section(SectionNames::METRICS)
    @include(ViewNames::METRICS)
@endsection

@section(SectionNames::CONTENT)
    <index-page></index-page>
@endsection