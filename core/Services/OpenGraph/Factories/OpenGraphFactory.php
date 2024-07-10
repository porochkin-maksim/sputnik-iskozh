<?php declare(strict_types=1);

namespace Core\Services\OpenGraph\Factories;

use Core\Resources\RouteNames;
use Core\Services\Images\StaticFileLocator;
use Core\Services\OpenGraph\Enums\OpenGraphType;
use Core\Services\OpenGraph\Models\OpenGraph;
use Illuminate\Support\Facades\Route;

class OpenGraphFactory
{
    public function default(): OpenGraph
    {
        $openGraph = new OpenGraph();
        $openGraph->setType(OpenGraphType::WEBSITE)
            ->setTitle(RouteNames::name(Route::current()?->getName(), env('APP_NAME')))
            ->setUrl(route(RouteNames::INDEX))
            ->setImage(StaticFileLocator::StaticFileService()->logoSnt()->getUrl())
            ->setDescription('Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь');

        return $openGraph;
    }
}
