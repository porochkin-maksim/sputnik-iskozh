<?php declare(strict_types=1);

namespace Core\App\User\Import;

readonly class AccountItem
{
    public function __construct(
        public string      $number,
        public ?float      $fraction,
    )
    {
    }
}
