<?php declare(strict_types=1);

namespace Core\Objects\File\Services;

use App\Models\File;
use Core\Objects\File\Factories\FileFactory;
use Core\Objects\File\Models\FileDTO;
use Core\Objects\File\Repositories\FileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

readonly class FileService
{
    public function __construct(
        private FileRepository $fileRepository,
        private FileFactory    $fileFactory,
    )
    {
    }

    public function store(
        UploadedFile $file,
        string       $directory = '',
        bool         $public = true,
    ): FileDTO
    {
        $baseDir = $public ? 'public/' : '';
        $path    = $file->storePublicly(sprintf('%s%s', $baseDir, $directory));

        $dto = new FileDto();
        $dto
            ->setName($file->getClientOriginalName())
            ->setExt($file->getClientOriginalExtension())
            ->setPath($path);

        return $dto;
    }

    public function removeFromStore(string $path): bool
    {
        return Storage::delete($path);
    }

    public function save(FileDTO $dto): FileDTO
    {
        $file = $this->fileFactory->makeModelFromDto($dto);
        $file = $this->fileRepository->save($file);

        return $this->fileFactory->makeDtoFromObject($file);
    }

    public function getById(int $id): ?FileDTO
    {
        $file = $this->fileRepository->getById($id);

        return $file ? $this->fileFactory->makeDtoFromObject($file) : null;
    }

    public function deleteById(int $id): bool
    {
        $file = $this->getById($id);
        if ($file) {
            if ($this->fileRepository->deleteById($id)) {
                $this->removeFromStore($file->getPath());
                return true;
            }
        }

        return false;
    }
}
