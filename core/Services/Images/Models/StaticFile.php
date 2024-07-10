<?php declare(strict_types=1);

namespace Core\Services\Images\Models;

readonly class StaticFile
{
    public function __construct(
        private string $name,
        private string $ext,
        private string $path,
        private string $url,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function toImage(): string
    {
        return <<<HTML
<a href="{$this->url}" data-lightbox="{$this->getName()}" class="d-block w-100">
    <image src="{$this->url}"></image>
</a>
HTML;
    }
}
