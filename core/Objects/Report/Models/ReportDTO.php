<?php declare(strict_types=1);

namespace Core\Objects\Report\Models;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;
use Core\Objects\Common\Traits\TimestampsTrait;
use Core\Objects\File\Models\FileDTO;
use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;

class ReportDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int          $id        = null;
    private ?string       $name      = null;
    private ?CategoryEnum $category  = null;
    private ?TypeEnum     $type      = null;
    private ?int          $year      = null;
    private ?Carbon       $startAt   = null;
    private ?Carbon       $endAt     = null;
    private ?float        $money     = null;
    private ?int          $version   = null;
    private ?int          $parent_id = null;

    /**
     * @var FileDTO[]
     */
    private array $files = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?CategoryEnum
    {
        return $this->category;
    }

    public function setCategory(?CategoryEnum $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?TypeEnum
    {
        return $this->type;
    }

    public function setType(?TypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getStartAt(): ?Carbon
    {
        return $this->startAt;
    }

    public function setStartAt(mixed $startAt): static
    {
        $this->startAt = DateTimeHelper::toCarbonOrNull($startAt);

        return $this;
    }

    public function getEndAt(): ?Carbon
    {
        return $this->endAt;
    }

    public function setEndAt(mixed $endAt): static
    {
        $this->endAt = DateTimeHelper::toCarbonOrNull($endAt);

        return $this;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(?float $money): static
    {
        $this->money = $money;

        return $this;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(?int $parent_id): static
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    /**
     * @return FileDTO[]
     */
    public function getFiles(): array
    {
        return $this->files ?? [];
    }

    /**
     * @param FileDTO[] $files
     */
    public function setFiles(array $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $dossier = new Dossier($this);

        $money = $this->money ? number_format($this->money, 2) : null;

        return [
            'dossier'   => $dossier,
            'id'        => $this->id,
            'name'      => $this->name,
            'category'  => $this->category?->value,
            'type'      => $this->type?->value,
            'year'      => $this->year,
            'start_at'  => $this->startAt?->format(DateTimeFormat::DATE_DEFAULT),
            'end_at'    => $this->endAt?->format(DateTimeFormat::DATE_DEFAULT),
            'money'     => $money,
            'version'   => $this->version,
            'parent_id' => $this->parent_id,
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::DATE_DEFAULT),
            'files'     => $this->getFiles(),
        ];
    }
}
