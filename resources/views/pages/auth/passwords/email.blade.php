<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;

?>

@extends('layouts.app-layout')

@section(SectionNames::CONTENT)
    <x-auth.form-card title="{{ __('Reset Password') }}">
        <restore-page></restore-page>
    </x-auth.form-card>
@endsection
