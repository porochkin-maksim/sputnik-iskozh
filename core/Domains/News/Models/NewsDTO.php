<?php declare(strict_types=1);

namespace Core\Domains\News\Models;

use Carbon\Carbon;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Files\Collections\FileCollection;
use Core\Domains\Files\Entities\FileEntity;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\NewsLocator;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;

class NewsDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int          $id          = null;
    private ?string       $title       = null;
    private ?string       $description = null;
    private ?string       $article     = null;
    private ?bool         $isLock      = null;
    private ?CategoryEnum $category    = null;
    private ?Carbon       $publishedAt = null;

    /**
     * @var FileEntity[]
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): NewsDTO
    {
        $this->description = $description;

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

    public function isLock(): bool
    {
        return (bool) $this->isLock;
    }

    public function setIsLock(?bool $isLock): static
    {
        $this->isLock = $isLock;

        return $this;
    }

    public function getCategory(): ?CategoryEnum
    {
        return $this->category;
    }

    public function setCategory(?CategoryEnum $category): NewsDTO
    {
        $this->category = $category;

        return $this;
    }

    public function getFiles(): FileCollection
    {
        return new FileCollection($this->files ?? []);
    }

    /**
     * @param FileEntity[] $files
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
            'description' => $this->description,
            'article'     => $this->article,
            'category'    => $this->getCategory()->value,
            'files'       => $this->getFiles(),
            'isLock'      => $this->isLock(),
            'publishedAt' => $this->getPublishedAt()?->format(DateTimeFormat::DATE_TIME_MAIN),
            'url'         => $this->getUrl(),
        ];
    }

    public function getUrl(): ?string
    {
        return NewsLocator::UrlFactory()->makeUrl($this);
    }

    public function getImages(): FileCollection
    {
        $result = [];
        foreach ($this->getFiles() as $file) {
            if ($file->isImage()) {
                $result[] = $file;
            }
        }

        return new FileCollection($result);
    }

    public function getArticleAsText(): string
    {
        return strip_tags((string) $this->getArticle());
    }
}
