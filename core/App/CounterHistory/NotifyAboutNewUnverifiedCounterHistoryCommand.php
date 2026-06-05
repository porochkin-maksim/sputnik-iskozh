<?php declare(strict_types=1);

namespace Core\App\CounterHistory;

use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Mails\NewCounterHistoryCreatedEmail;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Contracts\Mail\Mailer;

readonly class NotifyAboutNewUnverifiedCounterHistoryCommand
{
    public function __construct(
        private CounterHistoryService $counterHistoryService,
        private CounterService        $counterService,
        private AccountService        $accountService,
        private RoleService           $roleService,
        private HistoryChangesService $historyChangesService,
        private Mailer                $mailer,
    )
    {
    }

    public function execute(int $counterHistoryId): void
    {
        $counterHistory = $this->counterHistoryService->getById($counterHistoryId);
        if ($counterHistory === null || $counterHistory->isVerified()) {
            return;
        }

        $counter = $this->counterService->search(
            CounterSearcher::make()->setId($counterHistory->getCounterId()),
        )->getItems()->first();

        $previous = $this->counterHistoryService->getPrevious($counterHistory);
        $account  = $counter ? $this->accountService->getById($counter->getAccountId()) : null;

        if ($previous?->getValue() === $counterHistory->getValue()) {
            return;
        }

        $emails = $this->roleService->getEmailsByPermissions(PermissionEnum::COUNTERS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        foreach ($emails as $email) {
            $this->mailer->send(new NewCounterHistoryCreatedEmail(
                $email,
                $counterHistory,
                $counter,
                $previous,
                $account,
            ));

            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::COUNTER,
                $counter?->getId(),
                HistoryType::COUNTER_HISTORY,
                $counterHistory->getId(),
                text: 'Отправлено уведомление о новых показания счётчика на почту ' . $email,
            );
        }
    }
}
