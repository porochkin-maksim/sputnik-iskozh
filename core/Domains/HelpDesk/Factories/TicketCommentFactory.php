<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Factories;

use App\Models\HelpDesk\TicketComment;
use Core\Domains\HelpDesk\Models\TicketCommentDTO;
use Core\Domains\User\UserLocator;
use Illuminate\Database\Eloquent\Collection;

class TicketCommentFactory
{
    public function makeDtoFromObject(TicketComment $model): TicketCommentDTO
    {
        $dto = new TicketCommentDTO()
            ->setId($model->{TicketComment::ID})
            ->setTicketId($model->{TicketComment::TICKET_ID})
            ->setUserId($model->{TicketComment::USER_ID})
            ->setComment($model->{TicketComment::COMMENT})
            ->setIsInternal($model->{TicketComment::IS_INTERNAL})
            ->setCreatedAt($model->{TicketComment::CREATED_AT})
            ->setUpdatedAt($model->{TicketComment::UPDATED_AT})
        ;

        if (isset($model->getRelations()[TicketComment::RELATION_USER])) {
            $dto->setUser(UserLocator::UserFactory()->makeDtoFromObject($model->getRelation(TicketComment::RELATION_USER)));
        }

        if (isset($model->getRelations()[TicketComment::RELATION_TICKET])) {
            $ticketFactory = new TicketFactory();
            $dto->setTicket($ticketFactory->makeDtoFromObject($model->getRelation(TicketComment::RELATION_TICKET)));
        }

        return $dto;
    }

    public function makeDtoFromObjects(array|Collection $models): Collection
    {
        $result = new Collection();
        foreach ($models as $model) {
            $result->add($this->makeDtoFromObject($model));
        }

        return $result;
    }

    public function makeModelFromDto(TicketCommentDTO $dto, ?TicketComment $model): TicketComment
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = new TicketComment();
        }

        $result->fill([
            TicketComment::TICKET_ID   => $dto->getTicketId(),
            TicketComment::USER_ID     => $dto->getUserId(),
            TicketComment::COMMENT     => $dto->getComment(),
            TicketComment::IS_INTERNAL => $dto->getIsInternal(),
        ]);

        return $result;
    }
}
