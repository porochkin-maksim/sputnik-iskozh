<?php declare(strict_types=1);

namespace Core\Objects\News\Models;

use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;
use Core\Objects\Common\Traits\TimestampsTrait;
use Core\Objects\File\Models\FileDTO;
use Core\Resources\RouteNames;

class NewsDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id          = null;
    private ?string $title       = null;
    private ?string $article     = null;
    private ?Carbon $publishedAt = null;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(?string $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getPublishedAt(): ?Carbon
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(mixed $publishedAt): static
    {
        $this->publishedAt = DateTimeHelper::toCarbonOrNull($publishedAt);

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

        return [
            'dossier'     => $dossier,
            'id'          => $this->id,
            'title'       => $this->title,
            'article'     => $this->article,
            'files'       => $this->getFiles(),
            'publishedAt' => $this->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_DEFAULT),
            'url'         => route(RouteNames::NEWS_SHOW, $this->id),
        ];
    }
}
