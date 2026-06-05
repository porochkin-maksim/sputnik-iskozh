<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Service;

use Core\Domains\HelpDesk\Models\TicketServiceEntity;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Exceptions\ValidationException;
use Core\Contracts\StringServiceInterface;

readonly class SaveCommand
{
    public function __construct(
        private StringServiceInterface $stringService,
        private TicketCatalogService   $ticketServiceService,
        private SaveValidator          $saveValidator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(?int $id, int $categoryId, string $name, string $code, int $sortOrder, bool $isActive): ?TicketServiceEntity
    {
        if ($id) {
            $service = $this->ticketServiceService->getById($id);
            if ($service === null) {
                return null;
            }

            $service
                ->setCategoryId($categoryId)
                ->setName($name)
                ->setCode($this->stringService->slug($name))
                ->setSortOrder($sortOrder)
                ->setIsActive($isActive)
            ;

            $this->saveValidator->validate($service);

            return $this->ticketServiceService->save($service);
        }

        $service = (new TicketServiceEntity())
            ->setCategoryId($categoryId)
            ->setName($name)
            ->setCode($this->stringService->slug($name))
            ->setSortOrder($sortOrder)
            ->setIsActive($isActive)
        ;

        $this->saveValidator->validate($service);

        return $this->ticketServiceService->save($service);
    }
}
