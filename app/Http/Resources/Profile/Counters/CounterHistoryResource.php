<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Transactions\TransactionResource;
use Carbon\Carbon;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use Core\Responses\ResponsesEnum;
use lc;

readonly class CounterHistoryResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryDTO  $counterHistory,
        private ?CounterHistoryDTO $previousCounterHistory = null,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access      = lc::roleDecorator();
        $transaction = $this->counterHistory->getTransaction();

        $result = [
            'id'          => $this->counterHistory->getId(),
            'counterId'   => $this->counterHistory->getCounterId(),
            'value'       => $this->counterHistory->getValue(),
            'isVerified'  => $this->counterHistory->isVerified(),
            'before'      => $this->previousCounterHistory?->getValue(),
            'delta'       => $this?->previousCounterHistory ? ($this->counterHistory->getValue() - $this?->previousCounterHistory->getValue()) : null,
            'date'        => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'days'        => $this?->previousCounterHistory ? $this->counterHistory->getDate()?->diffInDays($this?->previousCounterHistory->getDate()) : null,
            'file'        => $this->counterHistory->getFile(),
            'actions'     => [
                ResponsesEnum::CREATE => ! $this?->previousCounterHistory || (bool) $this?->previousCounterHistory?->getDate()?->endOfDay()?->lte(Carbon::now()->endOfDay()),
            ],
            'transaction' => $transaction ? new TransactionResource($transaction) : null,
            'historyUrl'  => $this->counterHistory->getId()
                ? HistoryChangesLocator::route(
                    type         : HistoryType::COUNTER,
                    primaryId    : $this->counterHistory->getCounterId(),
                    referenceType: HistoryType::COUNTER_HISTORY,
                    referenceId  : $this->counterHistory->getId(),
                ) : null,
        ];

        if ($transaction && $access->can(PermissionEnum::INVOICES_VIEW)) {
            $result['invoiceUrl'] = route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $transaction->getInvoiceId()]);
        }

        return $result;
    }
}