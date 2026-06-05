<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Payment\SaveImportPaymentsCommand;
use Core\App\Billing\Payment\SaveImportPaymentsInput;
use Core\Domains\Billing\Events\ImportPaymentData;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Tests\TestCase;

class SaveImportPaymentsCommandTest extends TestCase
{
    private PaymentFactory            $paymentFactory;
    private PaymentService            $paymentService;
    private SaveImportPaymentsCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentFactory = new PaymentFactory;
        $this->paymentService = $this->createMock(PaymentService::class);

        $this->command = new SaveImportPaymentsCommand(
            $this->paymentFactory,
            $this->paymentService,
        );
    }

    public function test_execute_saves_valid_payments(): void
    {
        $this->paymentService->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->isVerified() === true
                                                           && $p->isModerated() === true
                                                           && $p->getName() === 'Импортированный платёж'
                                                           && in_array($p->getCost(), [1000.0, 2000.0], true),
            ))
        ;

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(1, 1000.0),
            new ImportPaymentData(2, 2000.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_skips_invalid_data(): void
    {
        $this->paymentService->expects($this->exactly(2))
            ->method('save')
        ;

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(0, 1000.0),
            new ImportPaymentData(1, 0.0),
            new ImportPaymentData(-1, 500.0),
            new ImportPaymentData(3, -10.0),
            new ImportPaymentData(5, 3000.0),
            new ImportPaymentData(6, 500.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_skips_all_invalid_data(): void
    {
        $this->paymentService->expects($this->never())->method('save');

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(0, 1000.0),
            new ImportPaymentData(1, 0.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_with_empty_input(): void
    {
        $this->paymentService->expects($this->never())->method('save');

        $input = new SaveImportPaymentsInput([]);

        $this->command->execute($input);
    }
}
