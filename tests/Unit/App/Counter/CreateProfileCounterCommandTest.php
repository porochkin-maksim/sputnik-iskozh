<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter;

use Carbon\Carbon;
use Core\App\Counter\CreateProfileCounterCommand;
use Core\App\Counter\Validator\CreateProfileCounterValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreateProfileCounterCommandTest extends TestCase
{
    private DbServiceInterface            $dbService;
    private CounterService                $counterService;
    private CounterHistoryService         $counterHistoryService;
    private FileService                   $fileService;
    private CreateProfileCounterValidator $validator;
    private CreateProfileCounterCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $counterFactory              = new CounterFactory;
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $counterHistoryFactory       = new CounterHistoryFactory;
        $this->fileService           = $this->createMock(FileService::class);
        $this->validator             = $this->createMock(CreateProfileCounterValidator::class);

        $this->command = new CreateProfileCounterCommand(
            $this->dbService,
            $this->counterService,
            $counterFactory,
            $this->counterHistoryService,
            $counterHistoryFactory,
            $this->fileService,
            $this->validator,
        );
    }

    public function test_execute_creates_counter_and_history(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedCounter = new CounterEntity;
        $savedCounter->setId(1);

        $this->counterService->expects($this->once())
            ->method('save')
            ->willReturn($savedCounter)
        ;

        $savedHistory = new CounterHistoryEntity;
        $savedHistory->setId(10);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($savedHistory)
        ;

        $this->fileService->expects($this->once())
            ->method('storeHistoryFile')
        ;

        $file = new UploadedFile('photo.jpg', '/tmp/photo.jpg', 'image/jpeg', 1024, 'content');
        $this->command->execute(1, '12345', 1, null, 100, $file, null);
    }

    public function test_execute_with_passport_file(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedCounter = new CounterEntity;
        $savedCounter->setId(1);

        $this->counterService->expects($this->once())
            ->method('save')
            ->willReturn($savedCounter)
        ;

        $savedHistory = new CounterHistoryEntity;
        $savedHistory->setId(10);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($savedHistory)
        ;

        $this->fileService->expects($this->once())->method('storeHistoryFile');
        $this->fileService->expects($this->once())->method('storePassportFile');

        $historyFile  = new UploadedFile('photo.jpg', '/tmp/photo.jpg', 'image/jpeg', 1024, 'content');
        $passportFile = new UploadedFile('passport.pdf', '/tmp/passport.pdf', 'application/pdf', 2048, 'content');
        $this->command->execute(1, '12345', 1, Carbon::now(), 100, $historyFile, $passportFile);
    }

    public function test_execute_throws_when_no_history_file(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['file' => ['Не передана фотография счётчика']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute(1, '12345', 1, null, 100, null, null);
    }
}
