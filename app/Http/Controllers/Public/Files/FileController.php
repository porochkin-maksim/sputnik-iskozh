<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\Files;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Shared\Files\FileResource;
use App\Http\Resources\Shared\ResourseList;
use Core\App\Files\DeleteCommand;
use Core\App\Files\DownCommand;
use Core\App\Files\GetListCommand;
use Core\App\Files\MoveCommand;
use Core\App\Files\ReplaceCommand;
use Core\App\Files\SaveCommand;
use Core\App\Files\StoreCommand;
use Core\App\Files\UpCommand;
use Core\Exceptions\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    private const string DIRECORY = '/uploads';

    public function __construct(
        private readonly StoreCommand   $storeCommand,
        private readonly SaveCommand    $saveCommand,
        private readonly DeleteCommand  $deleteCommand,
        private readonly UpCommand      $upCommand,
        private readonly DownCommand    $downCommand,
        private readonly MoveCommand    $moveCommand,
        private readonly ReplaceCommand $replaceCommand,
        private readonly GetListCommand $getListCommand,
    )
    {
    }

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canFiles();
    }

    public function index(): View
    {
        return view('public.files.index');
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $searchResult = $this->getListCommand->execute(
            $request->getIntOrNull('limit'),
            $request->getIntOrNull('parent_id'),
            $request->getStringOrNull('sort_by'),
            $request->getBool('sort_desc'),
        );

        return response()->json([
            'files' => new ResourseList($searchResult->getItems(), FileResource::class),
            'edit'  => $this->canEdit(),
        ]);
    }

    public function store(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->storeCommand->execute(
            $request->allFiles(),
            self::DIRECORY,
            $request->getIntOrNull('parent_id'),
        ));
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->saveCommand->execute(
            $request->getInt('id'),
            $request->getString('name'),
        ));
    }

    /**
     * @throws ValidationException
     */
    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->deleteCommand->execute($id));
    }

    /**
     * @throws ValidationException
     */
    public function replace(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->replaceCommand->execute(
            $request->getInt('id'),
            $request->file('file'),
            self::DIRECORY,
        ));
    }

    /**
     * @throws ValidationException
     */
    public function up(int $id, DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->upCommand->execute(
            $id,
            $request->getInt('index'),
        ));
    }

    /**
     * @throws ValidationException
     */
    public function down(int $id, DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->downCommand->execute(
            $id,
            $request->getInt('index'),
        ));
    }

    /**
     * @throws ValidationException
     */
    public function move(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->moveCommand->execute(
            $request->getInt('file'),
            $request->getInt('folder'),
            $request->getString('type') === 'copy',
        ));
    }
}
