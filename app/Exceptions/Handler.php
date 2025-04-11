<?php

namespace App\Exceptions;

use App\Mail\ExceptionMail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use lc;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logError($e);
        });
    }

    protected function logError(Throwable $e): void
    {
        $context = [
            'timestamp' => now()->toDateTimeString(),
            'userId'    => lc::user()->getId(),
            'userName'  => lc::userDecorator()->getFullName(),
            'url'       => request()?->fullUrl(),
            'method'    => request()?->method(),
            'input'     => request()?->all(),
            'headers'   => request()->headers->all(),
            'trace'     => $e->getTraceAsString(),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'code'      => $e->getCode(),
            'previous'  => $e->getPrevious() ? [
                'message' => $e->getPrevious()?->getMessage(),
                'file'    => $e->getPrevious()?->getFile(),
                'line'    => $e->getPrevious()?->getLine(),
            ] : null,
        ];

        Log::channel('errors')->error($e->getMessage(), $context);
    }
}
