<?php declare(strict_types=1);

namespace App\Console\Commands\Front;

use App\Resources\RouteNames;
use Core\Shared\Methods;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExportRouteFunctionsListCommand extends Command
{
    public function __construct(
        private readonly Router $router,
    )
    {
        parent::__construct();
    }

    protected const string OUTPUT_FILE_PATH = '/js/api/index.js';

    protected $signature   = 'front:export-route-functions-list-command';
    protected $description = 'Экспортирует маршруты в файл для фронта';

    public function handle(): void
    {
        $header    = $this->drawHeader();
        $functions = [];

        foreach ($this->getRoutes() as $route) {
            $name        = 'Api' . Str::ucfirst(Str::camel(Str::replace('.', '_', $route['name'])));
            $functions[] = $this->drawRouteFunction($name, $route);
        }

        $content = $header . "\n\n" . implode("\n\n", $functions);
        File::put(resource_path(self::OUTPUT_FILE_PATH), $content);
        $this->line("Routes exported to " . self::OUTPUT_FILE_PATH);
    }

    private function drawHeader(): string
    {
        return <<<JS
            import { makeQuery, prepareRequestData } from './helpers.js';
            JS;
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

    private function drawRouteFunction(string $name, array $route): string
    {
        $arguments = implode(',', array_keys($route['args']));
        $arguments = $arguments ? "$arguments, getParams = {}" : "getParams = {}";
        $method    = $route['method'];
        $uri       = str_replace(['{', '}'], ["'+", "+'"], $route['uri']);
        $note      = $route['name'];

        return <<<JS
            export function $name($arguments, postData = null) {
                // see $note
                return window.axios.$method(makeQuery('$uri', getParams), prepareRequestData(postData));
            }
            JS;
    }
}
