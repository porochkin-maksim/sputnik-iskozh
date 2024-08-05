<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\File;

use App\Http\Requests\AbstractRequest;
use App\Models\File\File;
use App\Models\File\Folder;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class MoveRequest extends AbstractRequest
{
    private const FILE   = RequestArgumentsEnum::FILE;
    private const FOLDER = RequestArgumentsEnum::FOLDER;
    private const TYPE   = RequestArgumentsEnum::TYPE;

    public function rules(): array
    {
        return [
            self::FILE   => [
                'required',
                Rule::exists(File::TABLE, 'id'),
            ],
            self::FOLDER => [
                'nullable',
                Rule::exists(Folder::TABLE, 'id'),
            ],
            self::TYPE   => [
                'required',
                'string',
            ],
        ];
    }

    public function getFileId(): int
    {
        return $this->getInt(self::FILE);
    }

    public function getFolderId(): ?int
    {
        return $this->getIntOrNull(self::FOLDER);
    }

    public function getType(): string
    {
        return $this->get(self::TYPE);
    }
}
