<?php declare(strict_types=1);

namespace App\Services\OpenGraph\Factories;

use App\Resources\RouteNames;
use App\Services\Images\StaticFileLocator;
use App\Services\OpenGraph\Enums\OpenGraphType;
use App\Services\OpenGraph\Models\OpenGraph;
use Illuminate\Support\Facades\Route;

class OpenGraphFactory
{
    public function default(): OpenGraph
    {
        $openGraph = new OpenGraph();
        $openGraph->setType(OpenGraphType::WEBSITE)
            ->setTitle(RouteNames::name(Route::current()?->getName(), config('app.name')))
            ->setUrl(route(RouteNames::INDEX))
            ->setImage(StaticFileLocator::StaticFileService()->logoSnt()->getUrl())
            ->setDescription('Садоводческое некоммерческое товарищество Спутник-Искож г. Тверь');

        return $openGraph;
    }
}
