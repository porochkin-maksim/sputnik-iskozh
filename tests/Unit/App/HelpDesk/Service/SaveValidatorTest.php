<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Service;

use Core\App\HelpDesk\Service\SaveValidator;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Responses\TicketServiceSearchResponse;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private TicketCatalogService $ticketServiceService;
    private SaveValidator        $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketServiceService = $this->createMock(TicketCatalogService::class);
        $this->validator            = new SaveValidator($this->ticketServiceService);
    }

    public function test_valid_data_passes(): void
    {
        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('Test Service')
            ->setCode('test-service')
        ;

        $this->ticketServiceService->method('search')->willReturn(
            (new TicketServiceSearchResponse)
                ->setItems(new TicketServiceCollection),
        );

        $this->validator->validate($service);

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_category_id_throws(): void
    {
        $service = (new TicketServiceEntity)
            ->setName('Test')
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_empty_name_throws(): void
    {
        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('')
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_name_too_long_throws(): void
    {
        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName(str_repeat('a', 101))
            ->setCode('test')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_empty_code_throws(): void
    {
        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('Test')
            ->setCode('')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_invalid_code_format_throws(): void
    {
        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('Test')
            ->setCode('BAD CODE!')
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_duplicate_code_in_same_category_throws(): void
    {
        $existing = (new TicketServiceEntity)->setId(2);

        $response = new TicketServiceSearchResponse;
        $response->setItems(new TicketServiceCollection([$existing]));

        $this->ticketServiceService->method('search')->willReturn($response);

        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('Test')
            ->setCode('test')
            ->setId(1)
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate($service);
    }

    public function test_same_code_allowed_when_same_entity(): void
    {
        $existing = (new TicketServiceEntity)->setId(1);

        $response = new TicketServiceSearchResponse;
        $response->setItems(new TicketServiceCollection([$existing]));

        $this->ticketServiceService->method('search')->willReturn($response);

        $service = (new TicketServiceEntity)
            ->setCategoryId(1)
            ->setName('Test')
            ->setCode('test')
            ->setId(1)
        ;

        $this->validator->validate($service);

        $this->expectNotToPerformAssertions();
    }

    public function test_validation_returns_all_errors(): void
    {
        $service = (new TicketServiceEntity)
            ->setName('')
            ->setCode('')
        ;

        try {
            $this->validator->validate($service);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('category_id', $e->errors);
            $this->assertArrayHasKey('name', $e->errors);
            $this->assertArrayHasKey('code', $e->errors);
        }
    }
}
