<?php declare(strict_types=1);

use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_PROFILE)

@section(SectionNames::CONTENT)
    <profile-block :account='@json(new AccountResource(lc::account()))'
                   :user='@json(new UserResource(lc::user()))'
    ></profile-block>
@endsection