<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Poll\Enums\QuestionType;

class QuestionDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int             $id      = null;
    private ?int             $pollId  = null;
    private ?QuestionType    $type    = null;
    private ?string          $text    = null;
    private ?QuestionOptions $options = null;
    private ?PollDTO         $poll    = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): QuestionDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getPollId(): ?int
    {
        return $this->pollId;
    }

    public function setPollId(?int $pollId): static
    {
        $this->pollId = $pollId;

        return $this;
    }

    public function getType(): ?QuestionType
    {
        return $this->type;
    }

    public function setType(?QuestionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getOptions(): ?QuestionOptions
    {
        return $this->options;
    }

    public function setOptions(null|string|QuestionOptions $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getPoll(): ?PollDTO
    {
        return $this->poll;
    }

    public function setPoll(?PollDTO $poll): static
    {
        $this->poll = $poll;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'     => $this->id,
            'pollId' => $this->pollId,
            'text'   => $this->text,
            'poll'   => $this->poll,
        ];
    }
}
