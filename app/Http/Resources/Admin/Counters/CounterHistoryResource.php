<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Claims\ClaimResource;
use Carbon\Carbon;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;
use Core\Responses\ResponsesEnum;

readonly class CounterHistoryResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryDTO $counterHistory,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access  = \lc::roleDecorator();
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
            'days'          => $previous ? $this->counterHistory->getDate()?->diffInDays($previous->getDate()) : null,
            'file'          => $this->counterHistory->getFile(),
            'counterNumber' => $counter?->getNumber(),
            'accountId'     => $counter?->getAccountId(),
            'accountNumber' => $counter?->getAccount()?->getNumber(),
            'isInvoicing'   => $counter?->isInvoicing(),
            'claim'         => $claim ? new ClaimResource($claim) : null,
            'actions'       => [
                ResponsesEnum::CREATE => $canCreateNew,
            ],
            'historyUrl'    => $this->counterHistory->getId()
                ? HistoryChangesLocator::route(
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