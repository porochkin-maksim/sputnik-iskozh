<?php

namespace App\Providers;

use Core\Env;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('relativeInclude', function ($args) {
            $args = Blade::stripParentheses($args);

            $viewBasePath = Blade::getPath();
            foreach ($this->app['config']['view.paths'] as $path) {
                if (substr($viewBasePath,0,strlen($path)) === $path) {
                    $viewBasePath = substr($viewBasePath,strlen($path));
                    break;
                }
            }

            $viewBasePath = dirname(trim($viewBasePath,'\/'));
            $args = substr_replace($args, $viewBasePath.'.', 1, 0);
            return "<?php echo \$__env->make({$args}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });
    }
}
