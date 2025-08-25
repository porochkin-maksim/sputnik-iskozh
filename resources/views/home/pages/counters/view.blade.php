<?php declare(strict_types=1);

use App\Http\Resources\Profile\Counters\CounterResource;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Diglactic\Breadcrumbs\Breadcrumbs;


/**
 * @var CounterDTO $counter
 */
$breadcrumbs = Breadcrumbs::generate(RouteNames::PROFILE_COUNTER_VIEW, $counter);
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::TITLE, $breadcrumbs->last()?->title)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::PROFILE_COUNTER_VIEW, $counter) }}
    <profile-counter-item :counter='@json(new CounterResource($counter))'></profile-counter-item>
@endsection