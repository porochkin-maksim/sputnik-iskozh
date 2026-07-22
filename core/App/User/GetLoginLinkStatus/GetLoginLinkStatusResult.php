<?php declare(strict_types=1);

namespace Core\App\User\GetLoginLinkStatus;

readonly class GetLoginLinkStatusResult
{
    public function __construct(
        public bool    $hasLink,
        public ?string $qrLink    = null,
        public ?string $tokenLink = null,
    )
    {
    }
}
