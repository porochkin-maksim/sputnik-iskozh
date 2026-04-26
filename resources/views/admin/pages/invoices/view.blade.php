<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use App\Resources\RouteNames;
use App\Resources\Views\SectionNames;
use Diglactic\Breadcrumbs\Breadcrumbs;

/**
 * @var InvoiceResource $invoice
 */
?>

@extends('layouts.admin-layout')

@section(SectionNames::CONTENT)
    {{ Breadcrumbs::render(RouteNames::ADMIN_INVOICE_VIEW, $invoice->getInvoice()) }}
    <invoice-item-view :invoice='@json($invoice)' />
@endsection