<?php declare(strict_types=1);

namespace Core\Domains\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\Models\NewsDTO;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID           = RequestArgumentsEnum::ID;
    private const TITLE        = RequestArgumentsEnum::TITLE;
    private const DESCRIPTION  = RequestArgumentsEnum::DESCRIPTION;
    private const ARTICLE      = RequestArgumentsEnum::ARTICLE;
    private const PUBLISHED_AT = RequestArgumentsEnum::PUBLISHED_AT;
    private const IS_LOCK      = RequestArgumentsEnum::IS_LOCK;
    private const CATEGORY     = RequestArgumentsEnum::CATEGORY;

    public function rules(): array
    {
        return [
            self::TITLE    => [
                'required',
                'string',
                'max:255',
            ],
            self::CATEGORY => [
                'required',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::TITLE . '.required'   => 'Укажите название',
            self::ARTICLE . '.required' => '',
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getTitle(): string
    {
        return $this->getString(self::TITLE);
    }

    public function getDescription(): ?string
    {
        return $this->getStringOrNull(self::DESCRIPTION);
    }

    public function getArticle(): ?string
    {
        return $this->get(self::ARTICLE);
    }

    public function getPublishedAt(): string
    {
        return $this->get(self::PUBLISHED_AT);
    }

    public function isLock(): bool
    {
        return $this->getBool(self::IS_LOCK);
    }

    public function getCategory(): CategoryEnum
    {
        return CategoryEnum::tryFrom($this->getInt(self::CATEGORY));
    }
}
