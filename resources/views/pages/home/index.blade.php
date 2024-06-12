<?php declare(strict_types=1);

use Core\Objects\Account\Models\AccountDTO;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var ?AccountDTO $account
 */
?>

@extends(ViewNames::LAYOUTS_APP)

@section(SectionNames::TITLE)
    Личный кабинет
@endsection

@section(SectionNames::CONTENT)
    @if($account)
        у вас есть аккаунт
    @else
        <register-account></register-account>
    @endif
@endsection