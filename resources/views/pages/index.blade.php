<?php declare(strict_types=1);

use Core\Resources\RouteNames;

?>

@extends('layouts.app')

@section('content')
    {{--    <ul>--}}
    {{--        <li>--}}
    {{--            <a href="{{ route(RouteNames::REPORTS) }}">Отчёты</a>--}}
    {{--        </li>--}}
    {{--    </ul>--}}

    {{--    <div class="card">--}}
    {{--        <div class="card-body">--}}

    {{--        </div>--}}
    {{--    </div>--}}
    <ul class="p-0 m-0">
        <li>
            <a href="{{ Storage::url('Устав.pdf') }}"
               target="_blank">
                Текущий устав.pdf
            </a>
            <br><br>
        </li>
        <li>
            <a href="{{ Storage::url('Постановление кассационного суда.pdf') }}"
               target="_blank">
                Постановление кассационного суда.pdf
            </a>
            <br><br>
        </li>
        <li>
            <a href="{{ Storage::url('Постановление кассационного суда.pdf') }}"
               target="_blank">
                Проект правил внутреннего распорядка.pdf
            </a>
        </li>
        <li>
            <a href="{{ Storage::url('Проект_УСТАВ_Спутник - Искож.pdf') }}"
               target="_blank">
                Проект устава.pdf
            </a>
        </li>
    </ul>
@endsection