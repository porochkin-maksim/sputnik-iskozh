<?php declare(strict_types=1);

namespace Core\Domains\News\Repositories;

use App\Models\File;
use Core\Db\RepositoryTrait;
use Core\Domains\File\Repositories\FileRepository as BaseFileRepository;

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
        return File::class;
    }
}
