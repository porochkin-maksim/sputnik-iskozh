<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models;

use Carbon\Carbon;
use Core\Domains\File\Collections\Files;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\NewsLocator;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\File\Models\FileDTO;
use Core\Resources\RouteNames;

class PollDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id           = null;
    private ?string $title        = null;
    private ?string $description  = null;
    private ?Carbon $startAt     = null;
    private ?Carbon $endAt       = null;
    private ?string $notifyEmails = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): PollDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getstartAt(): ?Carbon
    {
        return $this->startAt;
    }

    public function setstartAt(mixed $startAt): static
    {
        $this->startAt = DateTimeHelper::toCarbonOrNull($startAt);

        return $this;
    }

    public function getEndsAt(): ?Carbon
    {
        return $this->endAt;
    }

    public function setEndsAt(mixed $endAt): static
    {
        $this->endAt = DateTimeHelper::toCarbonOrNull($endAt);

        return $this;
    }

    public function getNotifyEmails(): ?string
    {
        return $this->notifyEmails;
    }

    public function setNotifyEmails(?string $notifyEmails): static
    {
        $this->notifyEmails = $notifyEmails;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'startAt'     => $this->startAt?->format(DateTimeFormat::DATE_TIME_FRONT),
            'endAt'       => $this->endAt?->format(DateTimeFormat::DATE_TIME_FRONT),
            'notifyEmails' => $this->notifyEmails,
        ];
    }
}
