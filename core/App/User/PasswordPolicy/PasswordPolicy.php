<?php declare(strict_types=1);

namespace Core\App\User\PasswordPolicy;

class PasswordPolicy
{
    public const int MIN_LENGTH = 8;

    public const string LOWERCASE_PATTERN = '/[a-z]/';
    public const string UPPERCASE_PATTERN = '/[A-Z]/';
    public const string DIGIT_PATTERN = '/\d/';
}