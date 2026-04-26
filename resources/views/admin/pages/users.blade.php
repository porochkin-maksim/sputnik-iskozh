<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
use Core\Domains\Access\PermissionEnum;

?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    <users-block></users-block>
@endsection