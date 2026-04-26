<?php declare(strict_types=1);

namespace App\Http\Resources\Shared\Files;

use App\Http\Resources\AbstractResource;
use Core\Domains\Folders\FolderEntity;
use App\Resources\RouteNames;

readonly class FolderResource extends AbstractResource
{
    public function __construct(
        private FolderEntity $entity,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'       => $this->entity->getId(),
            'uid'      => $this->entity->getUid(),
            'parentId' => $this->entity->getParentId(),
            'name'     => $this->entity->getName(),
            'url'      => route(RouteNames::FILES, ['folder' => $this->entity->getUid()]),
        ];
    }
}