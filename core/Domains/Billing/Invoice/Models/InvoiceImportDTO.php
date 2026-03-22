<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Models;

use Core\Resources\RouteNames;
use JsonSerializable;

class InvoiceImportDTO implements JsonSerializable
{
    public const string ACCOUNT_NUMBER = 'accountNumber';
    public const string ACCOUNT_ID     = 'accountId';
    public const string INVOICE_ID     = 'invoiceId';
    public const string INVOICE_COST   = 'invoiceCost';
    public const string INVOICE_PAID   = 'invoicePaid';
    public const string INVOICE_DEBT   = 'invoiceDebt';
    public const string COST           = 'cost';
    public const string PAID           = 'paid';
    public const string DEBT           = 'debt';

    public function __construct(
        private readonly mixed $accountNumber,
        private readonly mixed $accountId,
        private readonly mixed $invoiceId,
        private readonly mixed $invoiceCost,
        private readonly mixed $invoicePaid,
        private readonly mixed $invoiceDebt,
        private readonly mixed $cost,
        private readonly mixed $paid,
        private readonly mixed $debt,
    )
    {
    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array[self::ACCOUNT_NUMBER] ?? null,
            $array[self::ACCOUNT_ID] ?? null,
            $array[self::INVOICE_ID] ?? null,
            $array[self::INVOICE_COST] ?? null,
            $array[self::INVOICE_PAID] ?? null,
            $array[self::INVOICE_DEBT] ?? null,
            $array[self::COST] ?? null,
            $array[self::PAID] ?? null,
            $array[self::DEBT] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            self::ACCOUNT_NUMBER => $this->accountNumber ? : null,
            self::ACCOUNT_ID     => $this->accountId ? (int) $this->accountId : null,
            'accountUrl'         => $this->accountId ? route(RouteNames::ADMIN_ACCOUNT_VIEW, [$this->accountId]) : null,
            self::INVOICE_ID     => $this->invoiceId ? (int) $this->invoiceId : null,
            'invoiceUrl'         => $this->invoiceId ? route(RouteNames::ADMIN_INVOICE_VIEW, [$this->invoiceId]) : null,
            self::INVOICE_COST   => (float) $this->invoiceCost,
            self::INVOICE_PAID   => (float) $this->invoicePaid,
            self::INVOICE_DEBT   => (float) $this->invoiceDebt,
            self::COST           => (float) $this->cost,
            self::PAID           => (float) $this->paid,
            self::DEBT           => (float) $this->debt,
            'changeInCost'       => (float) $this->invoiceCost !== (float) $this->cost,
            'changeInPaid'       => (float) $this->invoicePaid !== (float) $this->paid,
            'changeInDebt'       => (float) $this->invoiceDebt !== (float) $this->debt,
        ];
    }
}
