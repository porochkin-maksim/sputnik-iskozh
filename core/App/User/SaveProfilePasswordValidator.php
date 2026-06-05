<?php declare(strict_types=1);

namespace Core\App\User;

class SaveProfilePasswordValidator
{
    public function __construct(
        private PasswordPolicyValidator $passwordPolicyValidator,
    )
    {
    }

    public function validate(?string $password): void
    {
        $this->passwordPolicyValidator->validate($password);
    }
}
