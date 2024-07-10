<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MinifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        return $this->minifyHTML($response);
    }

    private function minifyHTML($response)
    {
        if (App::isProduction()) {
            $buffer  = $response->getContent();
            $replace = [
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                '/ +/'                      => ' ',
            ];
            if (str_contains($buffer, '<pre>')) {
                $replace = [
                    '/<!--[^\[](.*?)[^\]]-->/s' => '',
                    "/<\?php/"                  => '<?php ',
                    "/\r/"                      => '',
                    "/>\n</"                    => '><',
                    "/>\s+\n</"                 => '><',
                    "/>\n\s+</"                 => '><',
                ];
            }
            $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
            try {
                $response->setContent($buffer);
            }
            catch (\Exception) {}
        }

        return $response;
    }
}
