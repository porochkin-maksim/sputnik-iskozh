<?php declare(strict_types=1);

use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Core\Domains\User\UserEntity;

/**
 * @var UserEntity $user
 */
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_USER_VIEW, $user) }}
    <user-item-view :id='@json($user->getId())' ></user-item-view>
@endsection
