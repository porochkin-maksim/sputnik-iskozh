<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class PaymentsListResource extends AbstractResource
{
    public function __construct(
        private PaymentCollection $paymentCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'payments'   => [],
            'historyUrl' => HistoryChangesLocator::route(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::PAYMENT,
            ),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::PAYMENTS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::PAYMENTS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::PAYMENTS_DROP),
            ],
        ];

        foreach ($this->paymentCollection as $payment) {
            $result['payments'][] = new PaymentResource($payment);
        }

        return $result;
    }
}
