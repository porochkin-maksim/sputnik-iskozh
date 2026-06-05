<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\AddProfileCounterHistoryCommand;
use Core\App\CounterHistory\AddProfileCounterHistoryValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AddProfileCounterHistoryCommandTest extends TestCase
{
    private DbServiceInterface                $dbService;
    private CounterService                    $counterService;
    private CounterHistoryService             $counterHistoryService;
    private FileService                       $fileService;
    private AddProfileCounterHistoryValidator $validator;
    private AddProfileCounterHistoryCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $counterHistoryFactory       = new CounterHistoryFactory;
        $this->fileService           = $this->createMock(FileService::class);
        $this->validator             = $this->createMock(AddProfileCounterHistoryValidator::class);

        $this->command = new AddProfileCounterHistoryCommand(
            $this->dbService,
            $this->counterService,
            $this->counterHistoryService,
            $counterHistoryFactory,
            $this->fileService,
            $this->validator,
        );
    }

    public function test_execute_adds_history(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(100);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $lastHistory = new CounterHistoryEntity;
        $lastHistory->setId(5)->setValue(100);

        $this->counterHistoryService->expects($this->once())
            ->method('getLastByCounterId')
            ->with(1)
            ->willReturn($lastHistory)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedHistory = new CounterHistoryEntity;
        $savedHistory->setId(6);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($savedHistory)
        ;

        $this->fileService->expects($this->once())->method('storeHistoryFile');

        $file   = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');
        $result = $this->command->execute(1, 150, $file, 100);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_counter_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $file = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');

        $this->expectException(HttpException::class);
        $this->command->execute(999, 150, $file, 100);
    }

    public function test_execute_throws_when_account_mismatch(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(200);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $file = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');

        $this->expectException(HttpException::class);
        $this->command->execute(1, 150, $file, 100);
    }
}
