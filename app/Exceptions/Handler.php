<?php

namespace App\Exceptions;

use App\Mail\ExceptionMail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            $this->sendEmail($e);
        });
    }

    public function sendEmail(Throwable $exception): void
    {
        try {
            $content['message'] = $exception->getMessage();
            $content['file']    = $exception->getFile();
            $content['line']    = $exception->getLine();
            $content['trace']   = $exception->getTrace();
            $content['url']     = request()->url();
            $content['body']    = request()->all();
            $content['ip']      = request()->ip();
            $content['user']    = request()->user()?->id ?? null;

            Mail::to(env('ADMIN_EMAIL'))->send(new ExceptionMail($content));
        }
        catch (Throwable $exception) {
            Log::error($exception);
        }
    }
}
