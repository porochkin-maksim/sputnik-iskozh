<?php declare(strict_types=1);

namespace Core\Domains\News;

use Carbon\Carbon;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Files\FileCollection;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class NewsEntity
{
    use TimestampsTrait;

    private ?int              $id          = null;
    private ?string           $title       = null;
    private ?string           $description = null;
    private ?string           $article     = null;
    private ?bool             $isLock      = null;
    private ?NewsCategoryEnum $category    = null;
    private ?Carbon           $publishedAt = null;

    private ?FileCollection $files = null;

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

    public function setDescription(?string $description): static
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

    public function getCategory(): ?NewsCategoryEnum
    {
        return $this->category;
    }

    public function setCategory(?NewsCategoryEnum $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getFiles(): FileCollection
    {
        return $this->files ? : new FileCollection();
    }

    public function setFiles(FileCollection $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function getImages(): FileCollection
    {
        return $this->getFiles()->getImages();
    }

    public function getArticleAsText(): string
    {
        return strip_tags((string) $this->getArticle());
    }
}
