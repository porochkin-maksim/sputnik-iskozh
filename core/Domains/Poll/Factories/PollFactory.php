<?php declare(strict_types=1);

namespace Core\Domains\Poll\Factories;

use App\Models\Polls\Poll;
use Carbon\Carbon;
use Core\Domains\Poll\Models\PollDTO;

readonly class PollFactory
{
    public function makeDefault(): PollDTO
    {
        $result = new PollDTO();
        $result->setTitle('Новый опрос');
        $result->setstartAt(Carbon::now()->addDays(2)->startOfDay());
        $result->setEndsAt(Carbon::now()->addDays(2)->addMonth()->endOfDay());

        return $result;
    }

    public function makeModelFromDto(PollDTO $dto, ?Poll $poll = null): Poll
    {
        if ($poll) {
            $result = $poll;
        }
        else {
            $result = Poll::make();
        }

        return $result->fill([
            Poll::TITLE         => $dto->getTitle(),
            Poll::DESCRIPTION   => $dto->getDescription(),
            Poll::start_at     => $dto->getstartAt(),
            Poll::end_at       => $dto->getEndsAt(),
            Poll::NOTIFY_EMAILS => $dto->getNotifyEmails(),
        ]);
    }

    public function makeDtoFromObject(Poll $poll): PollDTO
    {
        $result = new PollDTO();

        $result
            ->setId($poll->id)
            ->setTitle($poll->title)
            ->setDescription($poll->description)
            ->setstartAt($poll->start_at)
            ->setEndsAt($poll->end_at)
            ->setNotifyEmails($poll->notify_emails)
            ->setCreatedAt($poll->created_at)
            ->setUpdatedAt($poll->updated_at);

        return $result;
    }
}