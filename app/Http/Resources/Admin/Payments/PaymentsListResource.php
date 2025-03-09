<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

readonly class PaymentsListResource extends AbstractResource
{
    public function __construct(
        private PaymentCollection $paymentCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'payments'   => [],
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::INVOICE,
                referenceType: HistoryType::PAYMENT,
            ),
        ];

        foreach ($this->paymentCollection as $payment) {
            $result['payments'][] = new PaymentResource($payment);
        }

        return $result;
    }
}
