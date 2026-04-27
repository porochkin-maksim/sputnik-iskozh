<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Category;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Illuminate\Validation\ValidationException;

readonly class UpdateUseCase
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
        new SaveValidator()->validate($dto);

        return $this->ticketCategoryService->save($dto);
    }
}
