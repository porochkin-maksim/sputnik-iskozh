<?php declare(strict_types=1);

namespace Core\Services\Files\Models;

readonly class MetaData
{
    public function __construct(
        private ?bool   $timed_out,
        private ?bool   $blocked,
        private ?bool   $eof,
        private ?string $wrapper_type,
        private ?string $stream_type,
        private ?string $mode,
        private ?int    $unread_bytes,
        private ?bool   $seekable,
        private ?string $uri,
    )
    {
    }

    public function getTimedOut(): ?bool
    {
        return $this->timed_out;
    }

    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function getEof(): ?bool
    {
        return $this->eof;
    }

    public function getWrapperType(): ?string
    {
        return $this->wrapper_type;
    }

    public function getStreamType(): ?string
    {
        return $this->stream_type;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function getUnreadBytes(): ?int
    {
        return $this->unread_bytes;
    }

    public function getSeekable(): ?bool
    {
        return $this->seekable;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }
}
