<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use lc;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! lc::roleDecorator()->canAccessAdmin()) {
            abort(403, 'Доступ запрещен');
        }

        return $next($request);
    }
} 