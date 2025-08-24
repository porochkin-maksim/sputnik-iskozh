<?php declare(strict_types=1);

use App\Http\Resources\Profile\Counters\CounterResource;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var CounterDTO      $counter
 */
?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    <profile-counter-item :counter='@json(new CounterResource($counter))'></profile-counter-item>
@endsection