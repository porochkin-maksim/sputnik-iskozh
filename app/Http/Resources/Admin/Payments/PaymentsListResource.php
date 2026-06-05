<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Payment\PaymentCollection;

readonly class PaymentsListResource extends AbstractResource
{
    public function __construct(
        private PaymentCollection $paymentCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->paymentCollection as $payment) {
            $result[] = new PaymentResource($payment);
        }

        return $result;
    }
}
