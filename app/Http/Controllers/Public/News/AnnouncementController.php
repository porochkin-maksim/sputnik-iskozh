<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Shared\ResourseList;
use App\Locators\News\UrlFactory;
use Core\App\News\GetListCommand;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsFactory;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly NewsService    $newsService,
        private readonly NewsFactory    $newsFactory,
        private readonly GetListCommand $getListCommand,
        private readonly UrlFactory     $urlFactory,
    )
    {
    }

    public function index(): View
    {
        return view('public.announcement.index');
    }

    public function create(): JsonResponse
    {
        $news = $this->newsFactory
            ->makeDefault()
            ->setCategory(NewsCategoryEnum::ANNOUNCEMENT)
        ;

        return response()->json([
            'news'       => new NewsResource($news),
            'categories' => NewsCategoryEnum::json(),
        ]);
    }

    public function show($id): mixed
    {
        $id   = is_numeric($id) ? (int) $id : null;
        $news = $this->newsService->getById($id);
        $edit = Auth::id();

        if ( ! $news) {
            abort(404);
        }

        if ($news->getCategory() !== NewsCategoryEnum::ANNOUNCEMENT) {
            return redirect($this->urlFactory->makeUrl($news));
        }

        return view('public.news.show', compact('news', 'edit'));
    }

    /**
     * @throws ValidationException
     */
    public function list(DefaultRequest $request): JsonResponse
    {
        $canEdit = $this->canEdit();
        $news    = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSearch(),
            NewsCategoryEnum::ANNOUNCEMENT,
            true,
            null,
            ! $canEdit,
        );

        return response()->json([
            'news'  => new ResourseList($news->getItems(), NewsResource::class),
            'total' => $news->getTotal(),
            'edit'  => $canEdit,
        ]);
    }

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canNews();
    }
}
