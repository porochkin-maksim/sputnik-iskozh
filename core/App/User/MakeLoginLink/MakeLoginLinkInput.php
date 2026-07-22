<?php declare(strict_types=1);

namespace Core\App\User\MakeLoginLink;

readonly class MakeLoginLinkInput
{
    public function __construct(
        public int     $userId,
        public ?string $pin = null,
    )
    {
    }
}
