<?php declare(strict_types=1);

namespace Core\App\Account\Import;

readonly class AccountImportData
{
    public function __construct(
        public ?int    $id,
        public string  $number,
        public ?int    $size,
        public ?string $cadastreNumber,
    )
    {
    }
}
