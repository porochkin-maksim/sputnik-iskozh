<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter;

use Core\App\Counter\UpdateProfileCounterPassportCommand;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UpdateProfileCounterPassportCommandTest extends TestCase
{
    private DbServiceInterface                  $dbService;
    private CounterService                      $counterService;
    private FileService                         $fileService;
    private UpdateProfileCounterPassportCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService      = $this->createMock(DbServiceInterface::class);
        $this->counterService = $this->createMock(CounterService::class);
        $this->fileService    = $this->createMock(FileService::class);
        $this->command        = new UpdateProfileCounterPassportCommand(
            $this->dbService,
            $this->counterService,
            $this->fileService,
        );
    }

    public function test_execute_updates_passport(): void
    {
        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(100);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->fileService->expects($this->once())->method('deleteRelatedFileForCounterPassport');
        $this->fileService->expects($this->once())->method('storePassportFile');

        $file = new UploadedFile('passport.pdf', '/tmp/passport.pdf', 'application/pdf', 2048, 'content');
        $this->command->execute(1, 100, $file);
    }

    public function test_execute_throws_when_counter_not_found(): void
    {
        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $file = new UploadedFile('passport.pdf', '/tmp/passport.pdf', 'application/pdf', 2048, 'content');

        $this->expectException(HttpException::class);
        $this->command->execute(999, 100, $file);
    }

    public function test_execute_throws_when_account_mismatch(): void
    {
        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(200);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $file = new UploadedFile('passport.pdf', '/tmp/passport.pdf', 'application/pdf', 2048, 'content');

        $this->expectException(HttpException::class);
        $this->command->execute(1, 100, $file);
    }

    public function test_execute_throws_when_no_file(): void
    {
        $this->expectException(ValidationException::class);
        $this->command->execute(1, 100, null);
    }
}
