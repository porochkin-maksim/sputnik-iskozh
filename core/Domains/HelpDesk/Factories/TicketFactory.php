<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use App\Models\HelpDesk\Ticket;
use Core\Domains\Account\AccountLocator;
use Core\Domains\File\FileLocator;
use Core\Domains\HelpDesk\Collection\TicketCollection;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketDTO;
use Core\Domains\User\UserLocator;
use Illuminate\Database\Eloquent\Collection;

class TicketFactory
{
    public function makeDefault(): TicketDTO
    {
        return new TicketDTO();
    }

    public function makeDtoFromObject(Ticket $model): TicketDTO
    {
        $result = new TicketDTO()
            ->setId($model->{Ticket::ID})
            ->setUserId($model->{Ticket::USER_ID})
            ->setAccountId($model->{Ticket::ACCOUNT_ID})
            ->setType($model->{Ticket::TYPE})
            ->setCategoryId($model->{Ticket::CATEGORY_ID})
            ->setServiceId($model->{Ticket::SERVICE_ID})
            ->setPriority($model->{Ticket::PRIORITY})
            ->setStatus($model->{Ticket::STATUS})
            ->setDescription($model->{Ticket::DESCRIPTION})
            ->setResult($model->{Ticket::RESULT})
            ->setContactName($model->{Ticket::CONTACT_NAME})
            ->setContactPhone($model->{Ticket::CONTACT_PHONE})
            ->setContactEmail($model->{Ticket::CONTACT_EMAIL})
            ->setResolvedAt($model->{Ticket::RESOLVED_AT})
            ->setCreatedAt($model->{Ticket::CREATED_AT})
            ->setUpdatedAt($model->{Ticket::UPDATED_AT})
        ;

        if (isset($model->getRelations()[Ticket::RELATION_CATEGORY])) {
            $result->setCategory(HelpDeskServiceLocator::TicketCategoryFactory()->makeDtoFromObject($model->getRelation(Ticket::RELATION_CATEGORY)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_SERVICE])) {
            $result->setService(HelpDeskServiceLocator::TicketServiceFactory()->makeDtoFromObject($model->getRelation(Ticket::RELATION_SERVICE)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_ACCOUNT])) {
            $result->setAccount(AccountLocator::AccountFactory()->makeDtoFromObject($model->getRelation(Ticket::RELATION_ACCOUNT)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_USER])) {
            $result->setUser(UserLocator::UserFactory()->makeDtoFromObject($model->getRelation(Ticket::RELATION_USER)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_FILES])) {
            $result->setFiles(FileLocator::FileFactory()->makeDtoFromObjects($model->getRelation(Ticket::RELATION_FILES)));
        }

        if (isset($model->getRelations()[Ticket::RELATION_RESULT_FILES])) {
            $result->setResultFiles(FileLocator::FileFactory()->makeDtoFromObjects($model->getRelation(Ticket::RELATION_RESULT_FILES)));
        }

        return $result;
    }

    public function makeDtoFromObjects(array|Collection $models): TicketCollection
    {
        $result = new TicketCollection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }

    public function makeModelFromDto(TicketDTO $dto, ?Ticket $model): Ticket
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = new Ticket();
        }

        $result->fill([
            Ticket::USER_ID       => $dto->getUserId(),
            Ticket::ACCOUNT_ID    => $dto->getAccountId(),
            Ticket::TYPE          => $dto->getType(),
            Ticket::CATEGORY_ID   => $dto->getCategoryId(),
            Ticket::SERVICE_ID    => $dto->getServiceId(),
            Ticket::PRIORITY      => $dto->getPriority(),
            Ticket::STATUS        => $dto->getStatus(),
            Ticket::DESCRIPTION   => $dto->getDescription(),
            Ticket::RESULT        => $dto->getResult(),
            Ticket::CONTACT_NAME  => $dto->getContactName(),
            Ticket::CONTACT_PHONE => $dto->getContactPhone(),
            Ticket::CONTACT_EMAIL => $dto->getContactEmail(),
            Ticket::RESOLVED_AT   => $dto->getResolvedAt(),
        ]);

        return $result;
    }
}
