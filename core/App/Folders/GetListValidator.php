<?php declare(strict_types=1);

namespace Core\App\Folders;

use Core\Exceptions\ValidationException;

class GetListValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(?int $limit): void
    {
        if ($limit !== null && $limit <= 0) {
            throw new ValidationException(['limit' => ['Лимит должен быть больше нуля']]);
        }
    }
}
