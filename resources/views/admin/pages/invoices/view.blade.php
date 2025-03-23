<?php declare(strict_types=1);

use App\Http\Resources\Admin\Invoices\InvoiceResource;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var InvoiceResource $invoice
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <invoice-item-view
            :invoice='@json($invoice)'
    />
@endsection