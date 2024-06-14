<?php declare(strict_types=1);

use Core\Objects\Account\Models\AccountDTO;
use Core\Objects\User\Models\UserDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var AccountDTO $account
 * @var UserDTO    $user
 */
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::CONTENT)
    <profile-block :account='@json($account)'
                   :user='@json($user)'
    ></profile-block>
@endsection

@section(SectionNames::SUB)

@endsection