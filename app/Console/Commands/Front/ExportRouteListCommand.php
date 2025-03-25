<?php declare(strict_types=1);

namespace App\Console\Commands\Front;

use Core\Methods;
use Core\Resources\RouteNames;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;

class ExportRouteListCommand extends Command
{
    public function __construct(
        private readonly Router $router,
    )
    {
        parent::__construct();
    }

    protected const OUTPUT_FILE_PATH = '/js/routes.json';

    protected $signature   = 'front:export-route-list-command';
    protected $description = 'Экспортирует маршруты в файл для фронта';

    /**
     * @throws JsonException
     */
    public function handle(): void
    {
        $routes = [];

        foreach ($this->getRoutes() as $route) {
            $routes[Str::camel(Str::replace('.', '_', $route['name']))] = $route;
        }

        File::put(resource_path(self::OUTPUT_FILE_PATH), json_encode($routes, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        $this->line("Routes exported to " . self::OUTPUT_FILE_PATH);
    }

    private function filterMethod(array $methods): string
    {
        $array = array_filter($methods, static fn($method) => in_array($method, Methods::AVAILABLE_METHODS, true));

        return array_pop($array);
    }

    private function getRoutes(): array
    {
        return Arr::sort(
            $this->sortRoutes(
                collect($this->router->getRoutes())->reject(function (Route $route) {
                    return str_starts_with($route->uri(), '_') || ! $route->getName();
                })->map(function (Route $route) {
                    return $this->getRouteInformation($route);
                })->all(),
            ), static fn($route) => $route['name'],
        );
    }

    private function getRouteInformation(Route $route): array
    {
        return [
            'name'        => $route->getName(),
            'displayName' => RouteNames::name($route->getName()),
            'method'      => strtolower($this->filterMethod($route->methods())),
            'uri'         => sprintf('/%s', $route->uri() === '/' ? '' : Str::replace('?', '', $route->uri())),
            'args'        => $this->getUriParams($route->uri()),
        ];
    }

    /**
     * @param Collection|Route[] $routes
     *
     * @return Route[]
     */
    private function sortRoutes(array $routes): array
    {
        return Arr::sort($routes, static function ($route) {
            return $route['uri'];
        });
    }

    /**
     * @return array<string, string>
     */
    private function getUriParams(string $uri): array
    {
        $result = [];

        preg_match_all('/{(.*?)}/', $uri, $params);
        if (isset($params[0], $params[1])) {
            foreach ($params[1] as $key => $value) {
                $result[Str::replace('?', '', $value)] = Str::replace('?', '', $params[0][$key]);
            }
        }

        return $result;
    }
}
