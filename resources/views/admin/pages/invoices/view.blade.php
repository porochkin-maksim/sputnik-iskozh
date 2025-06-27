<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var InvoiceResource $invoice
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_INVOICE_VIEW, $invoice->getInvoice()) }}
    <invoice-item-view :invoice='@json($invoice)' />
@endsection