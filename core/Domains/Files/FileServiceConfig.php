<?php declare(strict_types=1);

namespace Core\Domains\Files;

readonly class FileServiceConfig
{
    public function __construct(
        public string        $baseDir = '',
        public ?FileTypeEnum $baseType = null,
        public bool          $defaultPublic = true,
    )
    {
    }
}
