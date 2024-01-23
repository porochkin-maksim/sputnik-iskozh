<?php declare(strict_types=1);

use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;

$categories = CategoryEnum::json();
$types      = TypeEnum::json();

?>

@extends('layouts.app')

@section('content')
    <report-list
        :categories='@json($categories)'
        :types='@json($types)'
    ></report-list>
@endsection