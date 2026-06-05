<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Service\LockService;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var PeriodEntity $period
 */

$isLocked = app(LockService::class)->isLocked(LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB);
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX, $period) }}
    @if($isLocked)
        <div class="alert-danger">
            <i class="fa fa-spinner fa-spin text-primary"></i> Система осуществляет импорт предыдущих данных. Попробуйте позднее.
        </div>
    @else
        <period-payments-import-block :period-id="{{ $period->getId() }}" ></period-payments-import-block>
    @endif
@endsection