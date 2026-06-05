<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Images\Services\StaticFileService;
use Carbon\Carbon;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\OptionService;
use Core\Domains\StateSchedule\StateSchedule;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PagesController extends Controller
{

    public function __construct(
        private readonly StaticFileService $staticFilesService,
        private readonly OptionService     $optionService,
    )
    {
    }

    public function index(): View
    {
        return view('pages.public.index');
    }

    public function contacts(): View
    {
        $month    = Carbon::now()->month;
        $isWinter = $month >= 11 || $month <= 3;

        $accountingData = $this->optionService->getByType(OptionEnum::SNT_ACCOUNTING)->getData();
        $chairmanData   = $this->optionService->getByType(OptionEnum::CHAIRMAN_INFO)->getData();
        $schedule       = StateSchedule::getScheduledDates(Carbon::now(), $isWinter ? 5 : 10);

        return view('pages.public.contacts.index', compact('accountingData', 'chairmanData', 'schedule'));
    }

    public function privacy(): View
    {
        return view('pages.public.privacy');
    }

    public function terms(): View
    {
        return view('pages.public.terms');
    }

    public function personalDataConsent(): View
    {
        return view('pages.public.personal-data-consent');
    }

    public function paymentsInfo(): View
    {
        return view('pages.public.payments-info');
    }

    public function cookiePolicy(): View
    {
        return view('pages.public.cookie');
    }

    public function garbage(): View
    {
        return view('pages.public.garbage');
    }

    public function regulation(): BinaryFileResponse
    {
        return response()->file($this->staticFilesService->regulation()->getStoragePath());
    }

    public function search(): View
    {
        return view('pages.public.search');
    }
}
