<?php declare(strict_types=1);

namespace Core\Domains\File\Factories;

use App\Models\File\Folder;
use Core\Domains\File\Models\FolderDTO;
use Illuminate\Database\Eloquent\Collection;

class FolderFactory
{
    public function makeModelFromDto(FolderDTO $dto, ?Folder $folder = null): Folder
    {
        if ( ! $folder) {
            $result = Folder::make();
        }
        else {
            $result = $folder;
        }

        if ($dto->getUid()) {
            $result->fill([
                Folder::UID => $dto->getUid(),
            ]);
        }

        return $result->fill([
            Folder::PARENT_ID => $dto->getParentId(),
            Folder::NAME      => $dto->getName(),
        ]);
    }

    public function makeDtoFromObject(Folder $folder): FolderDTO
    {
        $result = new FolderDTO();

        return $result
            ->setId($folder->id)
            ->setName($folder->name)
            ->setUid($folder->uid)
            ->setParentId($folder->parent_id)
            ->setCreatedAt($folder->created_at)
            ->setUpdatedAt($folder->updated_at);
    }

    /**
     * @param Folder[] $folders
     */
    public function makeDtoFromObjects(array|Collection $folders): array
    {
        $result = [];
        foreach ($folders as $folder) {
            $result[] = $this->makeDtoFromObject($folder);
        }

        return $result;
    }
}