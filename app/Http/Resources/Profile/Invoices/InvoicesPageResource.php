<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Invoices;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Resources\RouteNames;
use App\Services\Money\MoneyService;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;

readonly class InvoicesPageResource extends AbstractResource
{
    public function __construct(
        private PeriodCollection             $periods,
        private ?PeriodEntity                $period,
        private InvoiceCollection            $invoices,
        private PaymentCollection            $payments,
        private ?ServiceCollection           $services,
        private AccountEntity                $account,
        private bool                         $acquiringAvailable,
        private float                        $totalDebt,
        private bool                         $isViewingOther,
        private AccountEntity                $viewedAccount,
        private float                        $accountBalance = 0.0,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $formatMoney = static fn(mixed $amount): string => number_format((float) $amount, 2, ',', ' ') . ' ₽';

        $periodOptions = [];
        foreach ($this->periods as $p) {
            $periodOptions[] = [
                'id'       => $p->getId(),
                'name'     => $p->getName(),
                'selected' => $this->period && $this->period->getId() === $p->getId(),
            ];
        }

        $invoiceItems = [];
        foreach ($this->invoices as $invoice) {
            $claims = [];
            foreach ($invoice->getClaims()->sortByServiceTypes() as $claim) {
                $claims[] = [
                    'id'           => $claim->getId(),
                    'name'         => $claim->getName() ? : $claim->getService()?->getName() ? : $this->services?->getById($claim->getServiceId())?->getName(),
                    'tariff'       => $formatMoney($claim->getTariff()),
                    'quantity'     => $claim->getQuantity(),
                    'cost'         => $formatMoney($claim->getCost()),
                    'paid'         => $formatMoney($claim->getPaid()),
                    'delta'        => $formatMoney($claim->getDelta()),
                    'deltaNumeric' => (float) $claim->getDelta(),
                    'isPaid'       => $claim->isPaid(),
                ];
            }

            $paymentsItems = [];
            foreach ($invoice->getPayments() ? : [] as $payment) {
                $paymentsItems[] = [
                    'id'   => $payment->getId(),
                    'date' => $payment->getCreatedAt()->translatedFormat('d F Y H:i'),
                    'cost' => $formatMoney($payment->getCost()),
                ];
            }

            $acquiringAmounts = [];
            if ($this->acquiringAvailable && ! $invoice->getPaid() && ! $this->isViewingOther) {
                if (($this->account->getFraction() ? : 1) !== 1) {
                    $amount = MoneyService::toFloat(
                        MoneyService::parse($invoice->getDelta())->multiply($this->account->getFraction() ? : 1),
                    );

                    $acquiringAmounts[] = [
                        'label' => $formatMoney($amount),
                        'url'   => route(RouteNames::ACQURING_INVOICE_CREATE, [$invoice->getId(), $amount]),
                    ];
                }

                $acquiringAmounts[] = [
                    'label' => $formatMoney($invoice->getDelta()),
                    'url'   => route(RouteNames::ACQURING_INVOICE_CREATE, [$invoice->getId(), $invoice->getDelta()]),
                ];
            }

            $invoiceItems[] = [
                'id'           => $invoice->getId(),
                'periodId'     => $invoice->getPeriodId(),
                'periodName'   => $invoice->getPeriod()?->getName(),
                'title'        => $invoice->getName() ?: ($invoice->getType()?->isRegular() ? 'Основной' : null),
                'cost'         => $formatMoney($invoice->getCost()),
                'paid'         => $formatMoney($invoice->getPaid()),
                'delta'        => $formatMoney($invoice->getDelta()),
                'deltaNumeric' => (float) $invoice->getDelta(),
                'isPaid'       => $invoice->isPaid(),
                'claims'       => $claims,
                'payments'     => $paymentsItems,
                'acquiring'    => $acquiringAmounts,
                'paymentUrl'   => $this->isViewingOther ? '' : route(RouteNames::REQUESTS_PAYMENT, ['invoice' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]),
                'receiptUrl'   => route(RouteNames::DOCUMENT_RECEIPT_INVOICE, ['uid' => UidFacade::getUid(UidTypeEnum::INVOICE, $invoice->getId())]),
            ];
        }

        return [
            'periodOptions' => $periodOptions,
            'selectedPeriodId' => $this->period?->getId(),
            'invoiceItems'  => $invoiceItems,
            'totalDebt'     => $this->totalDebt,
            'acquiringAvailable' => $this->acquiringAvailable,
            'isViewingOther' => $this->isViewingOther,
            'viewedAccount' => $this->viewedAccount->getId() ? [
                'id'     => $this->viewedAccount->getId(),
                'number' => $this->viewedAccount->getNumber(),
            ] : null,
            'accountBalance' => $this->accountBalance,
        ];
    }
}
