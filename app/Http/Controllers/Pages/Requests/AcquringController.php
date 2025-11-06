<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use Core\Domains\Billing\Acquiring\AcquiringLocator;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use lc;

class AcquringController extends Controller
{
    private InvoiceService   $invoiceService;

    public function __construct()
    {
        $this->invoiceService = InvoiceLocator::InvoiceService();
    }

    public function create($invoiceId, $amount)
    {
        $invoiceId = (int) $invoiceId;
        $amount    = abs((float) $amount);

        $invoice = $this->invoiceService->getById($invoiceId);

        if ( ! $invoice || ! $amount) {
            return back()->with('error', 'Invoice not found');
        }
        $userId = lc::user()->getId();

        try {
            $link = AcquiringLocator::AcquiringWrapper($invoice, $amount, $userId)->getPaymentLink();

            return redirect($link);
        }
        catch (ProviderProcessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
