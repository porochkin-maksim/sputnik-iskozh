<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Acquiring;

use Core\App\Billing\Acquiring\HandleFailedWebhookCommand;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Acquiring\Services\ProviderGateway;
use Tests\TestCase;

class HandleFailedWebhookCommandTest extends TestCase
{
    private AcquiringService           $acquiringService;
    private ProviderGateway            $providerGateway;
    private HandleFailedWebhookCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->acquiringService = $this->createMock(AcquiringService::class);
        $this->providerGateway  = $this->createMock(ProviderGateway::class);
        $this->command          = new HandleFailedWebhookCommand($this->acquiringService, $this->providerGateway);
    }

    public function test_execute_cancels_acquiring(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(1000.0);
        $acquiring->setStatus(StatusEnum::PROCESS);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->providerGateway->expects($this->once())
            ->method('makeHash')
            ->with($acquiring)
            ->willReturn('hash')
        ;

        $this->acquiringService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(AcquiringEntity $e) => $e->getStatus() === StatusEnum::CANCELED))
        ;

        $result = $this->command->execute(1, 'hash');

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_not_found(): void
    {
        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(999, 'hash');

        $this->assertFalse($result);
    }

    public function test_execute_returns_false_when_hash_mismatch(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(1000.0);
        $acquiring->setStatus(StatusEnum::PROCESS);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->providerGateway->expects($this->once())
            ->method('makeHash')
            ->willReturn('correct-hash')
        ;

        $this->acquiringService->expects($this->never())->method('save');

        $result = $this->command->execute(1, 'wrong-hash');

        $this->assertFalse($result);
    }

    public function test_execute_returns_false_when_not_in_process(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(1000.0);
        $acquiring->setStatus(StatusEnum::PAID);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->providerGateway->expects($this->once())
            ->method('makeHash')
            ->with($acquiring)
            ->willReturn('hash')
        ;

        $this->acquiringService->expects($this->never())->method('save');

        $result = $this->command->execute(1, 'hash');

        $this->assertFalse($result);
    }
}
