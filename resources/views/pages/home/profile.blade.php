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

@extends(ViewNames::LAYOUTS_TWO_COLUMN)

@section(SectionNames::TITLE)
    Профиль
@endsection

@section(SectionNames::SUB)
    @relativeInclude('partial.nav')
@endsection

@section(SectionNames::MAIN)
    <profile-show :account='<?= json_encode($account) ?>'
                  :user='<?= json_encode($user) ?>'
    ></profile-show>
@endsection