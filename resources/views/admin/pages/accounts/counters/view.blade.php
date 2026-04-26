<?php declare(strict_types=1);

use App\Http\Resources\Admin\Counters\CounterResource;
use Core\Domains\Counter\CounterEntity;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;

/**
 * @var CounterEntity $counter
 */
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_COUNTER_VIEW, $counter) }}
    <counter-item-view :model-value='@json(new CounterResource($counter))'/>
@endsection
