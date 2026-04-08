<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Service;

use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly class CreateUseCase
{
    private TicketServiceService $ticketServiceService;

    public function __construct()
    {
        $this->ticketServiceService = HelpDeskServiceLocator::TicketServiceService();
    }

    /**
     * @throws ValidationException
     */
    public function execute(TicketServiceDTO $dto): TicketServiceDTO
    {
        $dto->setCode(Str::slug($dto->getName()));

        new SaveValidator()->validate($dto);

        return $this->ticketServiceService->save($dto);
    }
}
