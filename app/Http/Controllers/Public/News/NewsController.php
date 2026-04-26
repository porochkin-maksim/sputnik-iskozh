<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\News;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Public\NewsResource;
use App\Http\Resources\Public\NewsShortResource;
use App\Http\Resources\Shared\ResourseList;
use App\Locators\News\UrlFactory;
use Core\App\News\GetListCommand;
use Core\App\News\SaveFileCommand;
use Core\App\News\SaveCommand;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\News\FileService;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsFactory;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsService     $newsService,
        private readonly NewsFactory     $newsFactory,
        private readonly FileService     $fileService,
        private readonly GetListCommand  $getListCommand,
        private readonly SaveCommand     $saveCommand,
        private readonly UrlFactory      $urlFactory,
        private readonly SaveFileCommand $saveFileCommand,
    )
    {
    }

    public function index(): View
    {
        return view('public.news.index');
    }

    public function create(): JsonResponse
    {
        $news = $this->newsFactory->makeDefault();

        return response()->json([
            'news'       => new NewsResource($news),
            'categories' => NewsCategoryEnum::json(),
        ]);
    }

    public function show(int $id): mixed
    {
        $news = $this->newsService->getById($id);
        $edit = Auth::id();

        if ( ! $news) {
            abort(404);
        }

        if ($news->getCategory() !== NewsCategoryEnum::DEFAULT) {
            return redirect($this->urlFactory->makeUrl($news));
        }

        return view('public.news.show', compact('news', 'edit'));
    }

    public function edit(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $news = $this->newsService->getById($id);

        return response()->json([
            'news'       => new NewsResource($news),
            'categories' => NewsCategoryEnum::json(),
            'edit'       => $this->canEdit(),
        ]);
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
            NewsCategoryEnum::DEFAULT,
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

    /**
     * @throws ValidationException
     */
    public function lockedNews(DefaultRequest $request): JsonResponse
    {
        $news = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSearch(),
            null,
            false,
            true,
            ! $this->canEdit(),
        );

        return response()->json([
            'news'  => new ResourseList($news->getItems(), NewsShortResource::class),
            'total' => $news->getTotal(),
            'edit'  => false,
        ]);

    }

    /**
     * @throws ValidationException
     */
    public function indexList(DefaultRequest $request): JsonResponse
    {
        $news = $this->getListCommand->execute(
            10,
            $request->getOffset(),
            $request->getSearch(),
            null,
            true,
            false,
            ! $this->canEdit(),
        );

        return response()->json([
            'news'  => new ResourseList($news->getItems(), NewsShortResource::class),
            'total' => $news->getTotal(),
            'edit'  => false,
        ]);
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $news = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getString('title'),
            $request->getStringOrNull('description'),
            $request->getStringOrNull('article'),
            $request->getIntOrNull('category'),
            $request->getBool('is_lock'),
            $request->getStringOrNull('published_at'),
        );

        return response()->json($news);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->newsService->deleteById($id));
    }

    /** Работа с файлами */

    public function uploadFile(int $id, DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $this->fileService->storeAndSave($request->allFiles(), $id);

        return response()->json(true);
    }

    /**
     * @throws ValidationException
     */
    public function saveFile(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json(
            $this->saveFileCommand->execute(
                $request->getInt('id'),
                $request->getStringOrNull('name'),
            ),
        );
    }

    public function deleteFile(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $file = $this->fileService->getById($id);
        if ($file?->getType() !== FileTypeEnum::NEWS) {
            abort(403);
        }

        return response()->json($this->fileService->deleteById($id));
    }

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canNews();
    }
}
