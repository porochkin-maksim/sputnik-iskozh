<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Mail\Billing\NewPaymentCreatedEmail;
use App\Services\Queue\QueueEnum;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
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

    public function handle(
        PaymentService $paymentService,
        RoleService $roleService,
        HistoryChangesService $historyChangesService,
    ): void {
        $payment = $paymentService->getById($this->paymentId);

        if ( ! $payment || $payment->isVerified()) {
            return;
        }

        $emails = $roleService->getEmailsByPermissions(PermissionEnum::PAYMENTS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        foreach ($emails as $email) {
            Mail::send(new NewPaymentCreatedEmail($email, $payment));

            $historyChangesService->writeToHistory(
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
