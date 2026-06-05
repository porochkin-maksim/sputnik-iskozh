<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use App\Mail\Billing\NewPaymentCreatedEmail;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Contracts\Mail\Mailer;

readonly class NotifyAboutNewUnverifiedPaymentCommand
{
    public function __construct(
        private PaymentService        $paymentService,
        private RoleService           $roleService,
        private HistoryChangesService $historyChangesService,
        private Mailer                $mailer,
    )
    {
    }

    public function execute(int $paymentId): void
    {
        $payment = $this->paymentService->getById($paymentId);
        if ($payment === null || $payment->isVerified()) {
            return;
        }

        $emails = $this->roleService->getEmailsByPermissions(PermissionEnum::PAYMENTS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        foreach ($emails as $email) {
            $this->mailer->send(new NewPaymentCreatedEmail($email, $payment));

            $this->historyChangesService->writeToHistory(
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
