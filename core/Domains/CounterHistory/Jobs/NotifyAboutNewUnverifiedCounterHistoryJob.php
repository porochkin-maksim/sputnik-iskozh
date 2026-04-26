<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory\Jobs;

use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\QueueEnum;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Mails\NewCounterHistoryCreatedEmail;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAboutNewUnverifiedCounterHistoryJob implements ShouldQueue
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    protected static function getLockName(): LockNameEnum
    {
        return LockNameEnum::NOTIFY_ABOUT_NEW_UNVERIFIED_COUNTER_HISTORY_JOB;
    }

    protected function getIdentificator(): null|int|string
    {
        return $this->counterHistoryId;
    }

    public function process(
        CounterHistoryService $counterHistoryService,
        CounterService $counterService,
        AccountService $accountService,
        RoleService $roleService,
        HistoryChangesService $historyChangesService,
    ): void
    {
        $counterHistory = $counterHistoryService->getById($this->counterHistoryId);
        if ( ! $counterHistory || $counterHistory->isVerified()) {
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($counterHistory->getCounterId())
        ;

        $counter  = $counterService->search($counterSearcher)->getItems()->first();
        $previous = $counterHistoryService->getPrevious($counterHistory);
        $account  = null;

        if ($counter) {
            $account = $accountService->getById($counter->getAccountId());
        }

        $emails = $roleService->getEmailsByPermissions(PermissionEnum::COUNTERS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        if ($previous?->getValue() === $counterHistory->getValue()) {
            return;
        }

        foreach ($emails as $email) {
            $mail = new NewCounterHistoryCreatedEmail(
                $email,
                $counterHistory,
                $counter,
                $previous,
                $account,
            );
            Mail::send($mail);

            $historyChangesService->writeToHistory(
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
