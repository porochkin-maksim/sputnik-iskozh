<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use App\Mail\Billing\NewPaymentCreatedEmail;
use Core\App\Billing\Payment\NotifyAboutNewUnverifiedPaymentCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class NotifyAboutNewUnverifiedPaymentCommandTest extends TestCase
{
    private PaymentService                         $paymentService;
    private RoleService                            $roleService;
    private HistoryChangesService                  $historyChangesService;
    private Mailer                                 $mailer;
    private NotifyAboutNewUnverifiedPaymentCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        Config::shouldReceive('get')
            ->with('mail.emails.admin', null)
            ->andReturn('admin@snt.ru')
        ;

        $this->paymentService        = $this->createMock(PaymentService::class);
        $this->roleService           = $this->createMock(RoleService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->mailer                = $this->createMock(Mailer::class);

        $this->command = new NotifyAboutNewUnverifiedPaymentCommand(
            $this->paymentService,
            $this->roleService,
            $this->historyChangesService,
            $this->mailer,
        );
    }

    protected function tearDown(): void
    {
        Config::clearResolvedInstances();
        parent::tearDown();
    }

    public function test_execute_sends_notifications(): void
    {
        $payment = new PaymentEntity;
        $payment->setId(1)->setVerified(false);

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($payment)
        ;

        $this->roleService->expects($this->once())
            ->method('getEmailsByPermissions')
            ->with(PermissionEnum::PAYMENTS_EDIT)
            ->willReturn(['admin@test.com', 'moderator@test.com'])
        ;

        $this->mailer->expects($this->exactly(3))
            ->method('send')
            ->with($this->isInstanceOf(NewPaymentCreatedEmail::class))
        ;

        $this->historyChangesService->expects($this->exactly(3))
            ->method('writeToHistory')
        ;

        $this->command->execute(1);
    }

    public function test_execute_returns_early_when_payment_not_found(): void
    {
        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->roleService->expects($this->never())->method('getEmailsByPermissions');
        $this->mailer->expects($this->never())->method('send');
        $this->historyChangesService->expects($this->never())->method('writeToHistory');

        $this->command->execute(999);
    }

    public function test_execute_returns_early_when_payment_already_verified(): void
    {
        $payment = new PaymentEntity;
        $payment->setId(1)->setVerified(true);

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($payment)
        ;

        $this->roleService->expects($this->never())->method('getEmailsByPermissions');
        $this->mailer->expects($this->never())->method('send');
        $this->historyChangesService->expects($this->never())->method('writeToHistory');

        $this->command->execute(1);
    }
}
