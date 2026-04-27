<?php declare(strict_types=1);

namespace Core\Domains\File\Requests\Folder;

use App\Http\Requests\AbstractRequest;
use Core\Domains\File\Models\FolderDTO;

class SaveRequest extends AbstractRequest
{
    private const string ID        = 'id';
    private const string NAME      = 'name';
    private const string PARENT_ID = 'parent_id';

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
            ->setParentId($this->getParentId())
        ;

        return $dto;
    }
}
