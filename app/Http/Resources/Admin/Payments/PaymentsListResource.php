<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Payments;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\HistoryChanges\HistoryType;

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
            'historyUrl' => HistoryChangesRoute::make(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::PAYMENT,
            ),
            'actions'    => [
                'view' => $access->can(PermissionEnum::PAYMENTS_VIEW),
                'edit' => $access->can(PermissionEnum::PAYMENTS_EDIT),
                'drop' => $access->can(PermissionEnum::PAYMENTS_DROP),
            ],
        ];

        foreach ($this->paymentCollection as $payment) {
            $result['payments'][] = new PaymentResource($payment);
        }

        return $result;
    }
}
