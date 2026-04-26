<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Images\Services\StaticFileService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PagesController extends Controller
{

    public function __construct(private readonly StaticFileService $staticFilesService)
    {
    }

    public function index(): View
    {
        return view('public.index');
    }

    public function contacts(): View
    {
        return view('public.contacts');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function garbage(): View
    {
        return view('public.garbage');
    }

    public function regulation(): BinaryFileResponse
    {
        return response()->file($this->staticFilesService->regulation()->getStoragePath());
    }

    public function search(): View
    {
        return view('public.search');
    }
}
