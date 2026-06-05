<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\CreateInput;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Tests\TestCase;

class CreateInputTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $files = [
            new UploadedFile('test.pdf', '/tmp/test.pdf', 'application/pdf', 1024, 'content'),
        ];

        $input = new CreateInput(
            typeCode    : 'incident',
            categoryCode: 'electric',
            serviceCode : 'repair',
            description : 'test description',
            contactName : 'Ivan',
            contactEmail: 'ivan@test.com',
            contactPhone: '+79991234567',
            accountId   : 1,
            userId      : 2,
            files       : $files,
        );

        $this->assertSame('incident', $input->typeCode);
        $this->assertSame('electric', $input->categoryCode);
        $this->assertSame('repair', $input->serviceCode);
        $this->assertSame('test description', $input->description);
        $this->assertSame('Ivan', $input->contactName);
        $this->assertSame('ivan@test.com', $input->contactEmail);
        $this->assertSame('+79991234567', $input->contactPhone);
        $this->assertSame(1, $input->accountId);
        $this->assertSame(2, $input->userId);
        $this->assertCount(1, $input->files);
        $this->assertSame($files, $input->files);
    }
}
