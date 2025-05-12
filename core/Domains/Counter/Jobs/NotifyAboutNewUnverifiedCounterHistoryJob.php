<?php declare(strict_types=1);

namespace Core\Domains\Counter\Jobs;

use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Mails\NewCounterHistoryCreatedEmail;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAboutNewUnverifiedCounterHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $counterHistoryId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
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
        $emails = array_unique(array_merge($emails, [env('ADMIN_EMAIL')]));

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
