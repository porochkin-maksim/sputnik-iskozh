<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Services\OpenGraph\OpenGraphLocator;
use Illuminate\Support\Facades\Route;

$allowRobots = Route::is(RouteNames::INDEX)
               || Route::is(RouteNames::CONTACTS)
               || Route::is(RouteNames::ANNOUNCEMENTS)
               || Route::is(RouteNames::NEWS)
               || Route::is(RouteNames::GARBAGE)
               || Route::is(RouteNames::FILES)
               || Route::is(RouteNames::REQUESTS_PROPOSAL);

$openGraph = $openGraph ?? OpenGraphLocator::OpenGraphFactory()->default();

?>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible"
      content="ie=edge">
<meta name="csrf-token"
      content="{{ csrf_token() }}">
<meta name="description"
      content="{{ $openGraph->getDescription() }}">
<link rel="canonical"
      href="{{ $openGraph->getUrl() }}" />

@if ($allowRobots)
    <meta name="robots"
          content="index, nofollow, noarchive">
@else
    <meta name="robots"
          content="noindex">
@endif

{!! $openGraph->toMetaTags() !!}