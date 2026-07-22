<?php declare(strict_types=1);

namespace Core\App\User\MakeLoginLink;

readonly class MakeLoginLinkResult
{
    public function __construct(
        public string $qrLink,
        public string $tokenLink,
        public string $pin,
    )
    {
    }
}
