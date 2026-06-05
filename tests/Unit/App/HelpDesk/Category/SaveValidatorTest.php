<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Category;

use Core\App\HelpDesk\Category\SaveValidator;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Responses\TicketCategorySearchResponse;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private TicketCategoryService $ticketCategoryService;
    private SaveValidator         $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketCategoryService = $this->createMock(TicketCategoryService::class);
        $this->validator             = new SaveValidator($this->ticketCategoryService);
    }

    public function test_valid_data_passes(): void
    {
        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('Test Category')
            ->setCode('test-category')
        ;

        $this->ticketCategoryService->method('search')->willReturn(
            (new TicketCategorySearchResponse)
                ->setItems(new TicketCategoryCollection),
        );

        $this->validator->validate($category);

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_type_throws(): void
    {
        $category = (new TicketCategoryEntity)
            ->setName('Test')
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_empty_name_throws(): void
    {
        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('')
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_name_too_long_throws(): void
    {
        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName(str_repeat('a', 101))
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_empty_code_throws(): void
    {
        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('Test')
            ->setCode('')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_invalid_code_format_throws(): void
    {
        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('Test')
            ->setCode('INVALID CODE!')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_duplicate_code_in_same_type_throws(): void
    {
        $existing = (new TicketCategoryEntity)->setId(2);

        $response = new TicketCategorySearchResponse;
        $response->setItems(new TicketCategoryCollection([$existing]));

        $this->ticketCategoryService->method('search')->willReturn($response);

        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('Test')
            ->setCode('test')
            ->setId(1)
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($category);
    }

    public function test_same_code_allowed_when_same_entity(): void
    {
        $existing = (new TicketCategoryEntity)->setId(1);

        $response = new TicketCategorySearchResponse;
        $response->setItems(new TicketCategoryCollection([$existing]));

        $this->ticketCategoryService->method('search')->willReturn($response);

        $category = (new TicketCategoryEntity)
            ->setType(TicketTypeEnum::INCIDENT)
            ->setName('Test')
            ->setCode('test')
            ->setId(1)
        ;

        $this->validator->validate($category);

        $this->expectNotToPerformAssertions();
    }

    public function test_validation_returns_all_errors(): void
    {
        $category = (new TicketCategoryEntity)
            ->setName('')
            ->setCode('')
        ;

        try {
            $this->validator->validate($category);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('type', $e->errors);
            $this->assertArrayHasKey('name', $e->errors);
            $this->assertArrayHasKey('code', $e->errors);
        }
    }
}
