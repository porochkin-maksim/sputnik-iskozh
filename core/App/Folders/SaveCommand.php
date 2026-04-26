<?php declare(strict_types=1);

namespace Core\App\Folders;

use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private FolderService $folderService,
        private SaveValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(?int $id, string $name, ?int $parentId): FolderEntity
    {
        $this->validator->validate($name);

        if ($id !== null && ! $this->folderService->getById($id)) {
            throw new ValidationException(['id' => ['Указанная папка не существует']]);
        }

        if ($parentId !== null && ! $this->folderService->getById($parentId)) {
            throw new ValidationException(['parent_id' => ['Указанный родительский каталог не существует']]);
        }

        if ($id !== null && $id === $parentId) {
            throw new ValidationException(['parent_id' => ['Папка не может быть родителем самой себе']]);
        }

        $folder = (new FolderEntity())
            ->setId($id)
            ->setName(trim($name))
            ->setParentId($parentId);

        return $this->folderService->save($folder);
    }
}
