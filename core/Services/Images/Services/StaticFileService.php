<?php declare(strict_types=1);

namespace Core\Services\Images\Services;

use Core\Services\Images\Enums\StaticFileName;
use Core\Services\Images\Factories\StaticFileFactory;
use Core\Services\Images\Models\StaticFile;

readonly class StaticFileService
{
    public function __construct(
        private StaticFileFactory $staticFileFactory,
    )
    {
    }

    public function regulation(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::REGULATION);
    }

    public function logoSnt(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::LOGO_SNT);
    }
}
