<?php declare(strict_types=1);

namespace Core\Services\Images\Services;

use Carbon\Carbon;
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

    public function qrPayment(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::QR_PAYMENT);
    }

    public function seasonBgImage(): StaticFile
    {
        return match (Carbon::now()->month) {
            3, 4, 5   => $this->bgSpring(),
            6, 7, 8   => $this->bgSummer(),
            9, 10, 11 => $this->bgAutumn(),
            default   => $this->bgWinter(),
        };
    }

    private function bgSpring(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::BG_SPRING);
    }

    private function bgSummer(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::BG_SUMMER);
    }

    private function bgAutumn(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::BG_AUTUMN);
    }

    private function bgWinter(): StaticFile
    {
        return $this->staticFileFactory->make(StaticFileName::BG_WINTER);
    }
}
