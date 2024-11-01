<?php declare(strict_types=1);

namespace Core\Domains\File\Factories;

use App\Models\File\File;
use Core\Domains\File\Enums\TypeEnum;
use Core\Domains\File\Models\FileDTO;
use Illuminate\Database\Eloquent\Collection;

class FileFactory
{
    public function makeModelFromDto(FileDTO $dto, ?File $file = null): File
    {
        if ( ! $file) {
            $result = File::make();
        }
        else {
            $result = $file;
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

    public function makeDtoFromObject(File $file): FileDTO
    {
        $result = new FileDTO();

        return $result
            ->setId($file->id)
            ->setType($file->type ? TypeEnum::from($file->type) : null)
            ->setRelatedId($file->related_id)
            ->setParentId($file->parent_id)
            ->setOrder($file->order)
            ->setExt($file->ext)
            ->setName($file->name)
            ->setPath($file->path)
            ->setCreatedAt($file->created_at)
            ->setUpdatedAt($file->updated_at);
    }

    /**
     * @param File[] $files
     */
    public function makeDtoFromObjects(array|Collection $files): array
    {
        $result = [];
        foreach ($files as $file) {
            $result[] = $this->makeDtoFromObject($file);
        }

        return $result;
    }
}