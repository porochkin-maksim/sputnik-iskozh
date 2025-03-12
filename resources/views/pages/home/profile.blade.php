<?php declare(strict_types=1);

use App\Http\Resources\Accounts\AccountResource;
use App\Http\Resources\Admin\Users\UserResource;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <profile-block :account='@json(new AccountResource(app::account()))'
                   :user='@json(new UserResource(app::user()))'
    ></profile-block>
@endsection

@section(SectionNames::SUB)

@endsection