<?php declare(strict_types=1);?>

@extends('layouts.app')

@section('content')

    <index-page></index-page>

    <ul class="p-0 m-0 mt-5 list-unstyled">
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