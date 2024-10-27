<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Core\Resources\Views\ViewNames;
use Core\Services\Images\Services\StaticFileService;
use Core\Services\Images\StaticFileLocator;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PagesController extends Controller
{
    private StaticFileService $staticFilesService;

    public function __construct()
    {
        $this->staticFilesService = StaticFileLocator::StaticFileService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_INDEX);
    }

    public function contacts(): View
    {
        return view(ViewNames::PAGES_CONTACTS);
    }

    public function privacy(): View
    {
        return view(ViewNames::PAGES_PRIVACY);
    }

    public function garbage(): View
    {
        return view(ViewNames::PAGES_GARBAGE);
    }

    public function regulation(): BinaryFileResponse
    {
        return response()->file($this->staticFilesService->regulation()->getStoragePath());
    }

    public function rubrics(): View
    {
        return view(ViewNames::PAGES_RUBRICS);
    }

    public function proposal(): View
    {
        return view(ViewNames::PAGES_PROPOSAL);
    }
}
