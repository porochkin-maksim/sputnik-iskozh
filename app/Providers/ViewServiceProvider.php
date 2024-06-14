<?php

namespace App\Providers;

use Core\Objects\User\Models\UserDTO;
use Core\Objects\User\UserLocator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    private ?UserDTO $user;

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
        view()->composer('*', function ($view) {
            $this->user = $this->user ?? UserLocator::UserService()->searchById(Auth::id());

            $userDecorator = UserLocator::UserDecorator($this->user);
            $account       = $this->user?->getAccount();

            $view
                ->with('user', $this->user)
                ->with('userDecorator', $userDecorator)
                ->with('account', $account);
        });

        Blade::directive('relativeInclude', function ($args) {
            $args = Blade::stripParentheses($args);

            $viewBasePath = Blade::getPath();
            foreach ($this->app['config']['view.paths'] as $path) {
                if (str_starts_with($viewBasePath, $path)) {
                    $viewBasePath = substr($viewBasePath, strlen($path));
                    break;
                }
            }

            $viewBasePath = dirname(trim($viewBasePath, '\/'));
            $args         = substr_replace($args, $viewBasePath . '.', 1, 0);

            return "<?php echo \$__env->make({$args}, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });
    }
}
