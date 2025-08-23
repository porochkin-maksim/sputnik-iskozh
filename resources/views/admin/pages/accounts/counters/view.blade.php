<?php declare(strict_types=1);

use App\Http\Resources\Admin\Counters\CounterResource;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var CounterDTO $counter
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_COUNTER_VIEW, $counter) }}
    <counter-item-view :model-value='@json(new CounterResource($counter))'/>
@endsection