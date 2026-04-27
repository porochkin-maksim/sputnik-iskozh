<?php declare(strict_types=1);

namespace Core\Domains\News\Repositories;

use Core\Db\RepositoryTrait;
use Core\Infrastructure\File\FileModel;
use Core\Infrastructure\File\FileEloquentRepository as BaseFileRepository;

class FileRepository
{
    use RepositoryTrait;

    public function __construct(
        private readonly BaseFileRepository $fileRepository,
    )
    {
    }

    protected function modelClass(): string
    {
        return FileModel::class;
    }
}
