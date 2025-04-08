<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Jobs;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Billing\Payment\Mails\NewPaymentCreatedEmail;
use Core\Domains\Billing\Payment\PaymentLocator;
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

class NotifyAboutNewUnverifiedPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $paymentId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $payment = PaymentLocator::PaymentService()->getById($this->paymentId);

        if ( ! $payment || $payment->isVerified()) {
            return;
        }

        $emails = RoleLocator::RoleService()->getEmailsByPermissions(PermissionEnum::PAYMENTS_EDIT);
        $emails = array_unique(array_merge($emails, [env('ADMIN_EMAIL')]));

        foreach ($emails as $email) {
            $mail = new NewPaymentCreatedEmail(
                $email,
                $payment,
            );
            Mail::send($mail);

            HistoryChangesLocator::HistoryChangesService()->writeToHistory(
                Event::COMMON,
                HistoryType::INVOICE,
                null,
                HistoryType::PAYMENT,
                $payment->getId(),
                text: 'Отправлено уведомление о новом платеже на почту ' . $email,
            );
        }
    }
}
