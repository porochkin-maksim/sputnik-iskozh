<?php declare(strict_types=1);

namespace Core\Services\External\VTB;

readonly class ApiConfig
{
    public function __construct(
        private ?string $url = null,
        private ?string $userName = null,
        private ?string $password = null,
        private ?string $token = null,
    )
    {
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function isFilled(): bool
    {
        return $this->getUrl() && ($this->getToken() || ($this->getUserName() && $this->getPassword()));
    }
}
