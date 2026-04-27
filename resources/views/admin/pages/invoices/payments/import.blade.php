<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\LockLocator;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var PeriodDTO $period
 */

$isLocked = LockLocator::LockService()->isLocked(LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB);
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX, $period) }}
    @if($isLocked)
        <div class="alert-danger">
            <i class="fa fa-spinner fa-spin text-primary"></i> Система осуществляет импорт предыдущих данных. Попробуйте позднее.
        </div>
    @else
        <period-payments-import-block :period-id="{{ $period->getId() }}" />
    @endif
@endsection