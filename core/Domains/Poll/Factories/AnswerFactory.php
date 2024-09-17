<?php declare(strict_types=1);

namespace Core\Domains\Poll\Factories;

use App\Models\Polls\Answer;
use Core\Domains\Poll\Models\AnswerDTO;

readonly class AnswerFactory
{
    public function makeDefault(): AnswerDTO
    {
        return new AnswerDTO();
    }

    public function makeModelFromDto(AnswerDTO $dto, ?Answer $answer = null): Answer
    {
        if ($answer) {
            $result = $answer;
        }
        else {
            $result = Answer::make();
        }

        return $result->fill([
            Answer::QUESTION_ID => $dto->getQuestionId(),
            Answer::VALUE       => $dto->getValue(),
        ]);
    }

    public function makeDtoFromObject(Answer $answer): AnswerDTO
    {
        $result = new AnswerDTO();

        $result
            ->setId($answer->id)
            ->setQuestionId($answer->question_id)
            ->setValue($answer->value)
            ->setCreatedAt($answer->created_at)
            ->setUpdatedAt($answer->updated_at);

        return $result;
    }
}