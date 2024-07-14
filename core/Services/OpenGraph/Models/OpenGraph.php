<?php declare(strict_types=1);

namespace Core\Services\OpenGraph\Models;

use Core\Services\OpenGraph\Enums\OpenGraphType;
use Illuminate\Support\Str;

class OpenGraph
{
    private ?string $title       = null;
    private ?string $url         = null;
    private ?string $image       = null;
    private ?string $description = null;

    private ?OpenGraphType $type = null;

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setType(?OpenGraphType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return (string) $this->url;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = Str::substr(strip_tags($description), 0, 255);

        return $this;
    }

    public function toMetaTags(): string
    {
        $result = '';

        $result .= $this->title       ? sprintf('<meta property="og:title" content="%s"/>',       $this->title)       : '';
        $result .= $this->type        ? sprintf('<meta property="og:type" content="%s"/>',        $this->type->value) : '';
        $result .= $this->url         ? sprintf('<meta property="og:url" content="%s"/>',         $this->url)         : '';
        $result .= $this->image       ? sprintf('<meta property="og:image" content="%s"/>',       $this->image)       : '';
        $result .= $this->description ? sprintf('<meta property="og:description" content="%s"/>', $this->description) : '';

        return $result;
    }
}
