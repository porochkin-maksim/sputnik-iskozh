<?php declare(strict_types=1);

use App\Http\Resources\Profile\Counters\CounterResource;
use Core\Domains\Counter\CounterEntity;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var CounterEntity $counter
 */
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_COUNTER_VIEW, $counter);
?>

@extends('layouts.profile-layout')

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_COUNTER_VIEW, $counter) }}
    <counter-item :counter='@json(new CounterResource($counter))'></counter-item>
@endsection
