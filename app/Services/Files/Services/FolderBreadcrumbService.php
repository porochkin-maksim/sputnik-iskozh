<?php declare(strict_types=1);

namespace App\Services\Files\Services;

use App\Resources\RouteNames;
use Core\Domains\Folders\FolderCollection;
use Core\Domains\Folders\FolderService;
use Diglactic\Breadcrumbs\Breadcrumbs;

readonly class FolderBreadcrumbService
{
    public function __construct(
        private FolderService            $folderService,
        private FolderBreadcrumbRenderer $renderer,
    )
    {
    }

    public function buildHtml(int|null $folderId = null): string
    {
        $folders = $folderId
            ? $this->folderService->getWithParentsRecursively($folderId)->reverse()
            : new FolderCollection();

        $breadcrumbs = Breadcrumbs::generate(RouteNames::FILES);
        $items       = [];

        foreach ($folders as $folder) {
            $item        = new \stdClass();
            $item->title = $folder->getName();
            $item->url   = route(RouteNames::FILES, ['folder' => $folder->getUid()]);
            $breadcrumbs->add($item);
            $items[] = [
                'title' => $item->title,
                'url'   => $item->url,
            ];
        }

        return $this->renderer->render($items);
    }
}
