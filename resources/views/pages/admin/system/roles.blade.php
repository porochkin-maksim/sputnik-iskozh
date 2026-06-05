<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
use Core\Domains\Access\PermissionEnum;

?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    <roles-block :permissions='@json(PermissionEnum::getCases(true))'></roles-block>
@endsection