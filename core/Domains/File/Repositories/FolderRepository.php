<?php declare(strict_types=1);

namespace Core\Domains\File\Repositories;

use App\Models\File\Folder;
use Core\Db\RepositoryTrait;

class FolderRepository
{
    private const TABLE = Folder::TABLE;

    use RepositoryTrait;

    protected function modelClass(): string
    {
        return Folder::class;
    }

    public function save(Folder $folder): Folder
    {
        $folder->save();

        return $folder;
    }
}
