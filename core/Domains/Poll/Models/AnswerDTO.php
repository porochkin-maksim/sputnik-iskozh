<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models;

use Core\Domains\Common\Traits\TimestampsTrait;

class AnswerDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id;
    private ?int    $questionId;
    private ?string $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getQuestionId(): ?int
    {
        return $this->questionId;
    }

    public function setQuestionId(?int $questionId): static
    {
        $this->questionId = $questionId;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'questionId' => $this->questionId,
            'value'      => $this->value,
        ];
    }
}
