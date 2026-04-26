<?php declare(strict_types=1);

use App\Resources\Views\SectionNames;
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    <invoices-block></invoices-block>
@endsection