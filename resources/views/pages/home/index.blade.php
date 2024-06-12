<?php declare(strict_types=1);

use Core\Objects\Account\Models\AccountDTO;

/**
 * @var ?AccountDTO $account
 */
?>

@extends('layouts.app')

@section('title')
    Личный кабинет
@endsection

@section('content')
    @if($account)
        у вас есть аккаунт
    @else
        <register-account></register-account>
    @endif
@endsection