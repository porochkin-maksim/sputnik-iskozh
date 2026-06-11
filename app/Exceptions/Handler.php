<?php declare(strict_types=1);

namespace App\Exceptions;

use Core\Exceptions\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use lc;
use Symfony\Component\HttpFoundation\Response;
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

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors], 422);
            }

            // Для обычных запросов можно редиректить назад с ошибками, как делает Laravel
            return redirect()->back()->withErrors($e->errors);
        });

    }

    public function render($request, Throwable $e): Response
    {
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();

            if (view()->exists("errors.{$status}")) {
                $layout = match (true) {
                    str_starts_with($request->path(), 'admin')   => 'layouts.admin-layout',
                    str_starts_with($request->path(), 'profile') => 'layouts.profile-layout',
                    default                                      => 'layouts.app-layout',
                };

                return response()->view("errors.{$status}", [
                    'exception' => $e,
                    'layout'    => $layout,
                ], $status);
            }
        }

        return parent::render($request, $e);
    }

    protected function logError(Throwable $e): void
    {
        $request = request();

        $context = [
            'timestamp'     => now()->toDateTimeString(),
            'exceptionType' => get_class($e),
            'userId'        => lc::user()->getId(),
            'userName'      => lc::userDecorator()->getFullName(),
            'url'           => $request?->fullUrl(),
            'method'        => $request?->method(),
            'routeName'     => $request?->route()?->getName(),
            'trace'         => $this->normalizeTrace($e),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'code'          => $e->getCode(),
            'previous'      => $e->getPrevious() ? $this->normalizeException($e->getPrevious()) : null,
        ];

        Log::channel('errors')->error($e->getMessage(), $context);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeTrace(Throwable $e): array
    {
        return array_values(array_filter(array_map(
            fn (array $frame): ?array => $this->normalizeTraceFrame($frame),
            $e->getTrace(),
        )));
    }

    /**
     * @param array<string, mixed> $frame
     * @return array<string, mixed>|null
     */
    protected function normalizeTraceFrame(array $frame): ?array
    {
        $file = $frame['file'] ?? null;
        if ( ! is_string($file)) {
            return null;
        }

        if ( ! $this->isProjectFile($file)) {
            return null;
        }

        return [
            'file'     => $file,
            'line'     => $frame['line'] ?? null,
            'class'    => $frame['class'] ?? null,
            'type'     => $frame['type'] ?? null,
            'function' => $frame['function'] ?? null,
        ];
    }

    protected function normalizeException(Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'trace'   => $this->normalizeTrace($e),
        ];
    }

    protected function isProjectFile(string $file): bool
    {
        return str_starts_with($file, base_path('app'))
            || str_starts_with($file, base_path('core'))
            || str_starts_with($file, base_path('bootstrap'));
    }
}
