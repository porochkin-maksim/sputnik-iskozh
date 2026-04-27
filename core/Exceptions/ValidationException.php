<?php declare(strict_types=1);

namespace Core\Exceptions;

use Throwable;

class ValidationException extends \Exception
{
    public function __construct(
        public readonly array $errors,
        string                $message = "",
        int                   $code = 0,
        ?Throwable            $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
