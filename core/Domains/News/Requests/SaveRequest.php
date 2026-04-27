<?php declare(strict_types=1);

namespace Core\Domains\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\News\Enums\CategoryEnum;

class SaveRequest extends AbstractRequest
{
    private const string ID           = 'id';
    private const string TITLE        = 'title';
    private const string DESCRIPTION  = 'description';
    private const string ARTICLE      = 'article';
    private const string PUBLISHED_AT = 'published_at';
    private const string IS_LOCK      = 'is_lock';
    private const string CATEGORY     = 'category';

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
            self::DESCRIPTION    => [
                'nullable',
                'max:255',
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
