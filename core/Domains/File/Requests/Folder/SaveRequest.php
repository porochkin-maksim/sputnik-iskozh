<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\Folder;

use App\Http\Requests\AbstractRequest;
use Core\Domains\File\Models\FolderDTO;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID        = RequestArgumentsEnum::ID;
    private const NAME      = RequestArgumentsEnum::NAME;
    private const PARENT_ID = RequestArgumentsEnum::PARENT_ID;

    public function rules(): array
    {
        return [

        ];
    }

    public function messages(): array
    {
        return [

        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getParentId(): ?int
    {
        return $this->getIntOrNull(self::PARENT_ID);
    }

    public function getName(): string
    {
        return $this->get(self::NAME);
    }

    public function dto(): FolderDTO
    {
        $dto = new FolderDTO();

        $dto->setId($this->getId())
            ->setName($this->getName())
            ->setParentId($this->getParentId());

        return $dto;
    }
}
