<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Mails\NewCounterHistoryCreatedEmail;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Queue\DispatchIfNeededTrait;
use Core\Queue\QueueEnum;
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

    public function process(): void
    {
        $counterHistory = CounterLocator::CounterHistoryService()->getById($this->counterHistoryId);
        if ( ! $counterHistory || $counterHistory->isVerified()) {
            return;
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setId($counterHistory->getCounterId())
        ;

        $counter  = CounterLocator::CounterService()->search($counterSearcher)->getItems()->first();
        $previous = CounterLocator::CounterHistoryService()->getPrevious($counterHistory);
        $account  = null;

        if ($counter) {
            $account = AccountLocator::AccountService()->getById($counter->getAccountId());
        }

        $emails = RoleLocator::RoleService()->getEmailsByPermissions(PermissionEnum::COUNTERS_EDIT);
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

            HistoryChangesLocator::HistoryChangesService()->writeToHistory(
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
