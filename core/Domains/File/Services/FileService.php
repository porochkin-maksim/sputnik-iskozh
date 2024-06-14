<?php declare(strict_types=1);

namespace Core\Domains\File\Services;

use App\Models\File;
use Core\Domains\File\Collections\Files;
use Core\Domains\File\Factories\FileFactory;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\File\Repositories\FileRepository;
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
        $file = null;

        if ($dto->getId()) {
            $file = $this->fileRepository->getById($dto->getId());
        }

        $file = $this->fileFactory->makeModelFromDto($dto, $file);
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

    public function search(FileSearcher $searcher): Files
    {
        $reports = $this->fileRepository->search($searcher);

        $result  = new Files();
        foreach ($reports as $report) {
            $result->add($this->fileFactory->makeDtoFromObject($report));
        }

        return $result;
    }
}
