<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\Requests;

use App\Http\Controllers\Controller;
use Core\App\Billing\Acquiring\CreatePaymentLinkCommand;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use lc;

class AcquringController extends Controller
{
    public function __construct(
        private readonly CreatePaymentLinkCommand $command,
    )
    {
    }

    public function create(int $invoiceId, float $amount)
    {
        try {
            $link = $this->command->execute($invoiceId, abs($amount), lc::user()->getId());

            if ($link === null) {
                return back()->with('error', 'Invoice not found');
            }

            return redirect($link);
        } catch (ProviderProcessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
