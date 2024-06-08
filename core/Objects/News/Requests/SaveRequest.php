<?php declare(strict_types=1);

namespace Core\Objects\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\News\Models\NewsDTO;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID           = RequestArgumentsEnum::ID;
    private const TITLE        = RequestArgumentsEnum::TITLE;
    private const ARTICLE      = RequestArgumentsEnum::ARTICLE;
    private const TYPE         = RequestArgumentsEnum::TYPE;
    private const PUBLISHED_AT = RequestArgumentsEnum::PUBLISHED_AT;

    public function rules(): array
    {
        return [
            self::TITLE => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::TITLE . '.required'   => 'Укажите название',
            self::ARTICLE . '.required' => '',
            self::TYPE . '.required'    => '',
        ];
    }

    public function dto(): NewsDTO
    {
        $dto = new NewsDTO();
        $dto->setId($this->getInt(self::ID))
            ->setTitle($this->get(self::TITLE))
            ->setArticle($this->get(self::ARTICLE))
            ->setPublishedAt($this->getDateOrNull(self::PUBLISHED_AT));

        return $dto;
    }
}
