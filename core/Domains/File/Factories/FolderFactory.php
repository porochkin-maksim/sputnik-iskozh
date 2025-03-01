<?php declare(strict_types=1);

namespace Core\Domains\File\Factories;

use App\Models\File\Folder;
use Core\Domains\File\Models\FolderDTO;
use Illuminate\Database\Eloquent\Collection;

class FolderFactory
{
    public function makeModelFromDto(FolderDTO $dto, ?Folder $model = null): Folder
    {
        if ( ! $model) {
            $result = Folder::make();
        }
        else {
            $result = $model;
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

    public function makeDtoFromObject(Folder $model): FolderDTO
    {
        return (new FolderDTO())
            ->setId($model->id)
            ->setName($model->name)
            ->setUid($model->uid)
            ->setParentId($model->parent_id)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);
    }

    /**
     * @param Folder[] $models
     */
    public function makeDtoFromObjects(array|Collection $models): array
    {
        $result = [];
        foreach ($models as $model) {
            $result[] = $this->makeDtoFromObject($model);
        }

        return $result;
    }
}