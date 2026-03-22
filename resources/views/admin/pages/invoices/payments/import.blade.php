<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Domains\Billing\Period\Models\PeriodDTO;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var PeriodDTO $period
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_INVOICE_IMPORT_PAYMENTS_INDEX, $period) }}
    <period-payments-import-block :period-id="{{ $period->getId() }}" />
@endsection