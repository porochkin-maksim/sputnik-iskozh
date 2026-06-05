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
    <div class="page-hero">
        <h3 class="page-hero__title text-dark mb-0">Счётчик №{{ $counter->getNumber() }}</h3>
        <div class="page-hero__lead">
            Просмотр текущих показаний и истории изменений.
        </div>
    </div>
    <div class="page-section page-card p-3 p-lg-4">
        <counter-item :counter='@json(new CounterResource($counter))'></counter-item>
    </div>
@endsection
