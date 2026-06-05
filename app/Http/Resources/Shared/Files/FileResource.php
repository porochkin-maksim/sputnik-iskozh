<?php declare(strict_types=1);

namespace App\Http\Resources\Shared\Files;

use App\Http\Resources\AbstractResource;
use Core\Domains\Files\FileEntity;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

readonly class FileResource extends AbstractResource
{
    public function __construct(
        private FileEntity $entity,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'dossier'   => [
                'shortName' => $this->getShortName(),
                'createdAt' => $this->entity->getCreatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
            ],
            'id'        => $this->entity->getId(),
            'name'      => $this->entity->getName(),
            'ext'       => $this->entity->getExt(),
            'url'       => $this->getUrl(),
            'createdAt' => $this->entity->getCreatedAt()?->format(DateTimeFormat::DATE_DEFAULT),
            'isImage'   => $this->entity->isImage(),
        ];
    }

    private function getShortName(): string
    {
        $name = $this->entity->getOnlyName();
        if (Str::length($name) > 30) {
            $name = sprintf('%s%s%s',
                Str::substr($name, 0, 30 / 2),
                '[...]',
                Str::substr($name, Str::length($name) - 5),
            );
        }

        return sprintf('%s.%s', $name, $this->entity->getExt());
    }

    public function getUrl(): string|UrlGenerator|null
    {
        return $this->entity->getPath() ? url(Storage::url($this->entity->getPath())) : null;
    }
}