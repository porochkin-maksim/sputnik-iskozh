<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use Core\Enums\DateTimeFormat;
use Illuminate\Support\Str;

readonly class Dossier implements \JsonSerializable
{
    public function __construct(
        private FileDTO $file
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'shortName' => $this->getShortName(),
            'updatedAt' => $this->file->getUpdatedAt()?->format(DateTimeFormat::DATE_TIME_VIEW_FORMAT),
        ];
    }

    private function getShortName(int $length = 30): string
    {
        $name = $this->file->getOnlyName();
        if (Str::length($name) > $length) {
            $name = sprintf('%s%s%s',
                Str::substr($name, 0, $length / 2),
                '[...]',
                Str::substr($name, Str::length($name) - 5),
            );
        }

        return sprintf('%s.%s', $name, $this->file->getExt());
    }
}
