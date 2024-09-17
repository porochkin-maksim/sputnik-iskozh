<?php declare(strict_types=1);

namespace Core\Domains\Poll\Factories;

use App\Models\Polls\Question;
use Core\Domains\Poll\Models\QuestionDTO;

readonly class QuestionFactory
{
    public function makeDefault(): QuestionDTO
    {
        return new QuestionDTO();
    }

    public function makeModelFromDto(QuestionDTO $dto, ?Question $question = null): Question
    {
        if ($question) {
            $result = $question;
        }
        else {
            $result = Question::make();
        }

        return $result->fill([
            Question::POLL_ID => $dto->getPollId(),
            Question::TYPE    => $dto->getType()?->value,
            Question::TEXT    => $dto->getText(),
            Question::OPTIONS => $dto->getOptions(),
        ]);
    }

    public function makeDtoFromObject(Question $question): QuestionDTO
    {
        $result = new QuestionDTO();

        $result
            ->setId($question->id)
            ->setPollId($question->poll_id)
            ->setText($question->text)
            ->setOptions($question->options)
            ->setCreatedAt($question->created_at)
            ->setUpdatedAt($question->updated_at);

        return $result;
    }
}