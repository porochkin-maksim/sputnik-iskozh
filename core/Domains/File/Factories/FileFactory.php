<?php declare(strict_types=1);

namespace Core\Domains\File\Factories;

use App\Models\File\File;
use Core\Domains\File\Enums\TypeEnum;
use Core\Domains\File\Models\FileDTO;
use Illuminate\Database\Eloquent\Collection;

class FileFactory
{
    public function makeModelFromDto(FileDTO $dto, ?File $model = null): File
    {
        if ( ! $model) {
            $result = File::make();
        }
        else {
            $result = $model;
        }

        return $result->fill([
            File::TYPE       => $dto->getType()?->value,
            File::RELATED_ID => $dto->getRelatedId(),
            File::PARENT_ID  => $dto->getParentId(),
            File::ORDER      => $dto->getOrder(),
            File::EXT        => $dto->getExt(),
            File::NAME       => $dto->getName(),
            File::PATH       => $dto->getPath(),
        ]);
    }

    public function makeDtoFromObject(File $model): FileDTO
    {
        return (new FileDTO())
            ->setId($model->id)
            ->setType($model->type ? TypeEnum::from($model->type) : null)
            ->setRelatedId($model->related_id)
            ->setParentId($model->parent_id)
            ->setOrder($model->order)
            ->setExt($model->ext)
            ->setName($model->name)
            ->setPath($model->path)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);
    }

    /**
     * @param File[] $models
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