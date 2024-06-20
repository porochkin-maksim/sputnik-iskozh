<?php declare(strict_types=1);

namespace Core\Domains\File\Repositories;

use App\Models\File;
use Core\Db\RepositoryTrait;
use Core\Domains\File\Models\FileSearcher;
use Illuminate\Support\Facades\DB;

class FileRepository
{
    private const TABLE = File::TABLE;

    use RepositoryTrait;

    protected function modelClass(): string
    {
        return File::class;
    }

    public function save(File $file): File
    {
        $file->save();

        return $file;
    }
}
