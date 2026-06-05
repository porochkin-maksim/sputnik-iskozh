<?php declare(strict_types=1);

namespace App\Repositories\Shared\Relations;

use App\Models\HelpDesk\TicketCategory as TicketCategoryModel;
use App\Models\HelpDesk\TicketService as TicketServiceModel;
use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;

readonly class TicketServiceCategoryRelationAssembler
{
    public function __construct(
        private TicketServiceFactory  $ticketServiceFactory,
        private TicketCategoryFactory $ticketCategoryFactory,
    )
    {
    }

    public function makeServices(iterable $services): TicketServiceCollection
    {
        $collection = new TicketServiceCollection();
        foreach ($services as $service) {
            $collection->add($this->makeService($service));
        }

        return $collection;
    }

    public function makeCategories(iterable $categories): TicketCategoryCollection
    {
        $collection = new TicketCategoryCollection();
        foreach ($categories as $category) {
            $collection->add($this->makeCategory($category));
        }

        return $collection;
    }

    public function makeCategoryEntity(TicketCategoryModel $data): TicketCategoryEntity
    {
        return $this->makeCategory($data);
    }

    public function makeServiceEntity(TicketServiceModel $data): TicketServiceEntity
    {
        return $this->makeService($data);
    }

    private function makeService(TicketServiceModel $data): TicketServiceEntity
    {
        return $this->ticketServiceFactory->makeDefault()
            ->setId($data->id)
            ->setCategoryId($data->category_id)
            ->setName($data->name)
            ->setCode($data->code)
            ->setSortOrder($data->sort_order)
            ->setIsActive($data->is_active)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;
    }

    private function makeCategory(TicketCategoryModel $data): TicketCategoryEntity
    {
        return $this->ticketCategoryFactory->makeDefault()
            ->setId($data->id)
            ->setType(TicketTypeEnum::tryFrom($data->type))
            ->setName($data->name)
            ->setCode($data->code)
            ->setSortOrder($data->sort_order)
            ->setIsActive($data->is_active)
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at)
        ;
    }
}
