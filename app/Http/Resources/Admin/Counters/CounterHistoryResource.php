<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Claims\ClaimResource;
use App\Resources\RouteNames;
use App\Support\HistoryChangesRoute;
use Carbon\Carbon;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use lc;

readonly class CounterHistoryResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryEntity $counterHistory,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access  = lc::roleDecorator();
        $counter = $this->counterHistory->getCounter();

        $claim         = $this->counterHistory->getClaim();
        $previousValue = $this->counterHistory->getPreviousValue();
        $previous      = $this->counterHistory->getPrevious();
        $canCreateNew  = $this->counterHistory->getDate()?->endOfMonth()->endOfDay()?->lte(Carbon::now()->startOfDay());

        $result = [
            'id'            => $this->counterHistory->getId(),
            'counterId'     => $this->counterHistory->getCounterId(),
            'value'         => $this->counterHistory->getValue(),
            'isVerified'    => $this->counterHistory->isVerified(),
            'before'        => $previousValue,
            'delta'         => $this->counterHistory->getDelta(),
            'date'          => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'days'          => $previous ? abs((int) $this->counterHistory->getDate()?->diffInDays($previous->getDate())) : null,
            'file'          => $this->counterHistory->getFile(),
            'counterNumber' => $counter?->getNumber(),
            'accountId'     => $counter?->getAccountId(),
            'accountNumber' => $counter?->getAccount()?->getNumber(),
            'isInvoicing'   => $counter?->isInvoicing(),
            'claim'         => $claim ? new ClaimResource($claim) : null,
            'actions'       => [
                'create' => $canCreateNew,
            ],
            'historyUrl'    => $this->counterHistory->getId()
                ? HistoryChangesRoute::make(
                    referenceType: HistoryType::COUNTER_HISTORY,
                    referenceId  : $this->counterHistory?->getId(),
                ) : null,
            'accountUrl'    => $counter?->getAccountId() && $access->can(PermissionEnum::ACCOUNTS_VIEW)
                ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $counter?->getAccountId()])
                : null,
        ];

        if ($claim && $access->can(PermissionEnum::INVOICES_VIEW)) {
            $result['invoiceUrl'] = route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $claim->getInvoiceId()]);
        }

        return $result;
    }
}
