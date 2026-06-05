<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Category;

use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Exceptions\ValidationException;
use Core\Contracts\StringServiceInterface;

readonly class SaveCommand
{
    public function __construct(
        private StringServiceInterface $stringService,
        private TicketCategoryService  $ticketCategoryService,
        private SaveValidator          $saveValidator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(?int $id, int $type, string $name, string $code, int $sortOrder, bool $isActive): ?TicketCategoryEntity
    {
        if ($id) {
            $category = $this->ticketCategoryService->getById($id);
            if ($category === null) {
                return null;
            }

            $category->setType(TicketTypeEnum::tryFrom($type))
                ->setName($name)
                ->setCode($this->stringService->slug($name))
                ->setSortOrder($sortOrder)
                ->setIsActive($isActive)
            ;

            $this->saveValidator->validate($category);

            return $this->ticketCategoryService->save($category);
        }

        $category = (new TicketCategoryEntity())
            ->setType(TicketTypeEnum::tryFrom($type))
            ->setName($name)
            ->setCode($this->stringService->slug($name))
            ->setSortOrder($sortOrder)
            ->setIsActive($isActive)
        ;

        $this->saveValidator->validate($category);

        return $this->ticketCategoryService->save($category);
    }
}
