<?php declare(strict_types=1);

namespace Core\App\Files;

use App\Models\Files\FileModel;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService;
use Core\Repositories\SearcherInterface;

readonly class FileOrderingService
{
    public function __construct(
        private FileService $fileService,
    )
    {
    }

    public function saveFileOrderIndex(FileEntity $file, int $newIndex): void
    {
        $files = $this->fileService->search(new FileSearcher()
            ->setType($file->getType())
            ->setSortOrderProperty(FileModel::ORDER, SearcherInterface::SORT_ORDER_ASC)
            ->setRelatedId($file->getRelatedId()),
        )->getItems();

        $index = 0;
        $files->map(function (FileEntity $currentFile) use (&$index, $newIndex, $file) {
            if ($currentFile->getId() === $file->getId()) {
                $currentFile->setOrder($newIndex);
            }
            else {
                if ($index === $newIndex) {
                    $index++;
                }
                $currentFile->setOrder($index);
                $index++;
            }

            return $currentFile;
        });

        foreach ($files as $currentFile) {
            $this->fileService->save($currentFile);
        }
    }
}
