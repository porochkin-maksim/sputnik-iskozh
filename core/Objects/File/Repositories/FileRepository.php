<?php declare(strict_types=1);

namespace Core\Objects\File\Repositories;

use App\Models\File;
use Core\Db\RepositoryTrait;
use Core\Objects\File\Models\FileSearcher;
use Illuminate\Support\Facades\DB;

class FileRepository
{
    private const TABLE = File::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return File::class;
    }

    public function save(File $file): File
    {
        $file->save();

        return $file;
    }

    public function getById(?int $id): ?File
    {
        /** @var ?File $result */
        $result = $this->traitGetById($id);

        return $result;
    }

    /**
     * @return File[]
     */
    public function search(FileSearcher $searcher): array
    {
        $ids = DB::table(static::TABLE)
            ->pluck('id')
            ->toArray();

        return $this->traitGetByIds($ids, $searcher);
    }
}
