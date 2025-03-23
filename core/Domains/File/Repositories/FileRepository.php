<?php declare(strict_types=1);

namespace Core\Domains\File\Repositories;

use App\Models\File\File;
use Core\Db\RepositoryTrait;

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

    /**
     * @return int[]
     */
    public function getIdsByFullTextSearch(string $search): array
    {
        return File::select('id')->whereRaw(
            sprintf("MATCH(%s) AGAINST(? IN BOOLEAN MODE)",
                implode(',', [File::NAME]),
            ),
            $search
        )->get()->map(function (File $file) {
            return $file->id;
        })->unique()->toArray();
    }
}
