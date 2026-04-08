<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Category;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly class CreateUseCase
{
    private TicketCategoryService $ticketCategoryService;

    public function __construct()
    {
        $this->ticketCategoryService = HelpDeskServiceLocator::TicketCategoryService();
    }

    /**
     * @throws ValidationException
     */
    public function execute(TicketCategoryDTO $dto): TicketCategoryDTO
    {
        $dto->setCode(Str::slug($dto->getName()));

        new SaveValidator()->validate($dto);

        return $this->ticketCategoryService->save($dto);
    }
}