<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\InvoiceImportItem;
use Tests\TestCase;

class InvoiceImportItemTest extends TestCase
{
    public function test_constructor_from_array(): void
    {
        $data = [
            InvoiceImportItem::ACCOUNT_NUMBER  => '123',
            InvoiceImportItem::ACCOUNT_ID      => 1,
            InvoiceImportItem::INVOICE_ID      => 10,
            InvoiceImportItem::INVOICE_MAIN    => 100.0,
            InvoiceImportItem::INVOICE_COST    => 500.0,
            InvoiceImportItem::INVOICE_PAID    => 200.0,
            InvoiceImportItem::INVOICE_DELTA  => 50.0,
            InvoiceImportItem::INVOICE_DEBT   => 50.0,
            InvoiceImportItem::COST            => 550.0,
            InvoiceImportItem::PAID            => 250.0,
            InvoiceImportItem::DEBT            => 30.0,
        ];

        $item = InvoiceImportItem::fromArray($data);

        $serialized = $item->jsonSerialize();

        $this->assertSame('123', $serialized[InvoiceImportItem::ACCOUNT_NUMBER]);
        $this->assertSame(1, $serialized[InvoiceImportItem::ACCOUNT_ID]);
        $this->assertSame(10, $serialized[InvoiceImportItem::INVOICE_ID]);
        $this->assertSame(100.0, $serialized[InvoiceImportItem::INVOICE_MAIN]);
        $this->assertSame(500.0, $serialized[InvoiceImportItem::INVOICE_COST]);
        $this->assertSame(200.0, $serialized[InvoiceImportItem::INVOICE_PAID]);
        $this->assertSame(50.0, $serialized[InvoiceImportItem::INVOICE_DELTA]);
        $this->assertSame(50.0, $serialized[InvoiceImportItem::INVOICE_DEBT]);
        $this->assertSame(550.0, $serialized[InvoiceImportItem::COST]);
        $this->assertSame(250.0, $serialized[InvoiceImportItem::PAID]);
        $this->assertSame(30.0, $serialized[InvoiceImportItem::DEBT]);
        $this->assertTrue($serialized['changeInCost']);
        $this->assertTrue($serialized['changeInPaid']);
        $this->assertTrue($serialized['changeInDelta']);
    }

    public function test_from_array_with_nulls(): void
    {
        $item = InvoiceImportItem::fromArray([]);

        $serialized = $item->jsonSerialize();

        $this->assertNull($serialized[InvoiceImportItem::ACCOUNT_NUMBER]);
        $this->assertNull($serialized[InvoiceImportItem::ACCOUNT_ID]);
        $this->assertNull($serialized[InvoiceImportItem::INVOICE_ID]);
        $this->assertNull($serialized['accountUrl']);
        $this->assertNull($serialized['invoiceUrl']);
        $this->assertSame(0.0, $serialized[InvoiceImportItem::INVOICE_MAIN]);
        $this->assertSame(0.0, $serialized[InvoiceImportItem::COST]);
        $this->assertFalse($serialized['changeInCost']);
        $this->assertFalse($serialized['changeInPaid']);
        $this->assertFalse($serialized['changeInDelta']);
    }
}
