<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\UploadedFile;

class ReplaceRequest extends AbstractRequest
{
    private const ID = RequestArgumentsEnum::ID;

    public function getFileId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file('file');
    }
}
