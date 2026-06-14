<?php declare(strict_types=1);

use App\Resources\RouteNames;

/**
 * @var bool $locked
 */
?>

@extends('layouts.admin-layout')

@section('title', 'Импорт участков')

@section('content')
    {{ Breadcrumbs::render(RouteNames::ADMIN_ACCOUNT_IMPORT_INDEX) }}
    <div class="container-fluid px-3">
        <div class="row">
            <div class="col-12">
                @if($locked)
                    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                        <i class="fa fa-warning fa-lg" aria-hidden="true"></i>
                        <div>
                            <p class="mb-0">Система осуществляет импорт предыдущих данных. Пожалуйста, подождите.</p>
                        </div>
                    </div>
                @else
                    <div id="accounts-import-block"
                         data-locked="{{ $locked ? 'true' : 'false' }}"
                    >
                        <accounts-import-block></accounts-import-block>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
