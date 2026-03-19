<?php declare(strict_types=1);
$exception = $exception ?? null;
$message   = $exception?->getMessage() ? : __('Not Found');
?>

@extends('errors::minimal')

@section('title', $message)
@section('code', '404')
@section('message', $message)
