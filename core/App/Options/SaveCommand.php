<?php declare(strict_types=1);

namespace Core\App\Options;

use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionFactory;
use Core\Domains\Option\OptionService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private SaveValidator $validator,
        private OptionService $optionService,
        private OptionFactory $optionFactory,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(int $id, array $data): OptionEntity
    {
        $this->validator->validate($id, $data);

        $option = $this->optionService->getById($id);
        $type = OptionEnum::tryFrom($id);

        $option->setData($this->optionFactory->makeDataDtoFromArray($type, $data));

        return $this->optionService->save($option);
    }
}
