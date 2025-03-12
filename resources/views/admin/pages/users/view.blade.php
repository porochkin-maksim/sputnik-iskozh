<?php declare(strict_types=1);

use App\Http\Resources\Admin\Roles\RolesSelectResource;
use App\Http\Resources\Admin\Users\UserResource;
use App\Http\Resources\Common\SelectOptionResource;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var UserResource         $user
 * @var SelectOptionResource $accounts
 * @var RolesSelectResource  $roles
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <user-item-view
            :user='@json($user)'
            :accounts='@json($accounts)'
            :roles='@json($roles)'
    />
@endsection