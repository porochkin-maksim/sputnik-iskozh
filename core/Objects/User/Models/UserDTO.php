<?php declare(strict_types=1);

namespace Core\Objects\User\Models;

class UserDTO
{
    private ?int    $id            = null;
    private ?string $name          = '';
    private ?string $email         = '';
    private ?string $password      = null;
    private ?bool   $rememberToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRememberToken(): ?bool
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?bool $rememberToken): static
    {
        $this->rememberToken = $rememberToken;

        return $this;
    }
}
