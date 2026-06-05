<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\UpdateInput;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Tests\TestCase;

class UpdateInputTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $files       = [new UploadedFile('ticket.pdf', '/tmp/ticket.pdf', 'application/pdf', 2048, 'content')];
        $resultFiles = [new UploadedFile('result.pdf', '/tmp/result.pdf', 'application/pdf', 1024, 'result')];

        $input = new UpdateInput(
            id          : 1,
            description : 'updated description',
            result      : 'done',
            type        : 2,
            categoryId  : 3,
            serviceId   : 4,
            priority    : 2,
            status      : 4,
            contactName : 'Petr',
            contactPhone: '+79991112233',
            contactEmail: 'petr@test.com',
            userId      : 5,
            accountId   : 6,
            files       : $files,
            resultFiles : $resultFiles,
        );

        $this->assertSame(1, $input->id);
        $this->assertSame('updated description', $input->description);
        $this->assertSame('done', $input->result);
        $this->assertSame(2, $input->type);
        $this->assertSame(3, $input->categoryId);
        $this->assertSame(4, $input->serviceId);
        $this->assertSame(2, $input->priority);
        $this->assertSame(4, $input->status);
        $this->assertSame('Petr', $input->contactName);
        $this->assertSame('petr@test.com', $input->contactEmail);
        $this->assertSame('+79991112233', $input->contactPhone);
        $this->assertSame(5, $input->userId);
        $this->assertSame(6, $input->accountId);
        $this->assertSame($files, $input->files);
        $this->assertSame($resultFiles, $input->resultFiles);
    }
}
