<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Decorator;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Infra\Comparator\DTO\Changes;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Resources\RouteNames;
use lc;

readonly class HistoryChangesDecorator
{
    private LogData $logData;

    public function __construct(
        private HistoryChangesDTO $historyChanges,
    )
    {
        $this->logData = $this->historyChanges->getLog();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getChanges(): array
    {
        $result = [];

        if ( ! $this->logData->getChanges()) {
            return $result;
        }

        foreach ($this->logData->getChanges()->getIterator() as $item) {
            $result[] = new Changes(
                $this->getTitle($item->getTitle()),
                $this->geOldValue($item->getOldValue()),
                $this->getNewValue($item->getNewValue()),
            );
        }

        return $result;
    }

    public function getText(): ?string
    {
        return $this->logData->getText();
    }

    public function getEvent(): Event
    {
        return $this->logData->getEvent();
    }

    public function getActionEventText(): string
    {
        $type = $this->historyChanges->getReferenceType()?->name() ? : $this->historyChanges->getType()?->name();
        $id   = $this->historyChanges->getReferenceId() ? : $this->historyChanges->getPrimaryId();

        return match ($this->logData->getEvent()) {
            Event::CREATE => sprintf('Создана запись «%s» id:%s', $type, $id),
            Event::UPDATE => sprintf('Обновлена запись «%s» id:%s', $type, $id),
            Event::DELETE => sprintf('Удалена запись «%s» id:%s', $type, $id),
            Event::COMMON => (string) $this->logData->getText(),
        };
    }

    private function getTitle(string $title): string
    {
        return $title;
    }

    private function geOldValue(string $value): string
    {
        return $value;
    }

    private function getNewValue(string $value): string
    {
        return $value;
    }

    public function getPrimaryUrl(): ?string
    {
        $h = $this->historyChanges;

        $accountId = null;
        if ($h->getType() === HistoryType::COUNTER) {
            $accountId = CounterLocator::CounterService()->getById((int) $h->getPrimaryId())?->getAccountId();
        }

        if ($h->getType() === HistoryType::INVOICE) {
            if ($h->getPrimaryId() === null) {
                switch ($h->getReferenceType()) {
                    case HistoryType::PAYMENT:
                    {
                        $id = PaymentLocator::PaymentService()->getById($h->getReferenceId())?->getInvoiceId();
                        break;
                    }
                    case HistoryType::CLAIM:
                    {
                        $id = ClaimLocator::ClaimService()->getById($h->getReferenceId())?->getInvoiceId();
                        break;
                    }
                    default:
                    {
                        $id = null;
                    }
                }

                if ($id) {
                    $h->setPrimaryId($id);
                    HistoryChangesLocator::HistoryChangesService()->save($h);
                }
                else {
                    return null;
                }
            }
        }

        $role = lc::roleDecorator();

        return match ($h->getType()) {
            HistoryType::PERIOD  => $role->can(PermissionEnum::PERIODS_VIEW) ? route(RouteNames::ADMIN_PERIOD_INDEX) : null,
            HistoryType::ACCOUNT => $role->can(PermissionEnum::ACCOUNTS_VIEW) ? route(RouteNames::ADMIN_ACCOUNT_VIEW, $h->getPrimaryId()) : null,
            HistoryType::INVOICE => $role->can(PermissionEnum::INVOICES_VIEW) ? route(RouteNames::ADMIN_INVOICE_VIEW, $h->getPrimaryId()) : null,
            HistoryType::USER    => $role->can(PermissionEnum::USERS_VIEW) ? route(RouteNames::ADMIN_USER_VIEW, $h->getPrimaryId()) : null,
            HistoryType::ROLE    => $role->can(PermissionEnum::ROLES_VIEW) ? route(RouteNames::ADMIN_ROLE_INDEX) : null,
            HistoryType::COUNTER => $role->can(PermissionEnum::COUNTERS_VIEW) && $accountId ? route(RouteNames::ADMIN_COUNTER_VIEW, [$accountId, $h->getPrimaryId()]) : null,
            HistoryType::OPTION  => $role->can(PermissionEnum::OPTIONS_VIEW) ? route(RouteNames::ADMIN_OPTIONS_INDEX, $h->getPrimaryId()) : null,
            default              => null,
        };
    }

    public function getReferenceUrl(): ?string
    {
        $h = $this->historyChanges;

        $accountId = null;
        if ($h->getReferenceType() === HistoryType::COUNTER_HISTORY) {
            $accountId = CounterLocator::CounterService()->getById((int) $h->getPrimaryId())?->getAccountId();
            if ( ! $accountId) {
                return null;
            }
        }

        $role = lc::roleDecorator();

        return match ($h->getType()) {
            HistoryType::SERVICE         => $role->can(PermissionEnum::SERVICES_VIEW) ? route(RouteNames::ADMIN_SERVICE_INDEX) : null,
            HistoryType::CLAIM,
            HistoryType::PAYMENT         => $role->can(PermissionEnum::INVOICES_VIEW) ? route(RouteNames::ADMIN_INVOICE_VIEW, $accountId) : null,
            HistoryType::COUNTER_HISTORY => $role->can(PermissionEnum::COUNTERS_VIEW) && $accountId && $h->getPrimaryId() ? route(RouteNames::ADMIN_COUNTER_VIEW, [$accountId, $h->getPrimaryId()]) : null,
            default                      => null,
        };
    }
}
