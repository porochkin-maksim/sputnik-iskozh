<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DefaultRequest;
use App\Models\Infra\HistoryChanges;
use App\Support\HistoryChangesDecoratorFactory;
use Core\Domains\HistoryChanges\HistoryChangesSearcher;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Repositories\SearcherInterface;

class HistoryChangesViewController
{
    private const int MAX_LIMIT = 1000;

    public function __construct(
        private readonly HistoryChangesService          $historyChangesService,
        private readonly HistoryChangesDecoratorFactory $decoratorFactory,
        private readonly UserService                    $userService,
    )
    {
    }

    public function __invoke(DefaultRequest $request)
    {
        $type          = $request->getIntOrNull('type');
        $primaryId     = $request->getIntOrNull('primary_id') ? : $request->getIntOrNull('primaryId');
        $referenceType = $request->getIntOrNull('reference_type') ? : $request->getIntOrNull('referenceType');
        $referenceId   = $request->getIntOrNull('reference_id') ? : $request->getIntOrNull('referenceId');
        $userId        = $request->getIntOrNull('user_id');
        $isExclude     = $request->getBool('exclude');
        $limit         = min($request->getIntOrNull('limit') ?? 25, self::MAX_LIMIT);
        $offset        = max($request->getIntOrNull('skip') ?? 0, 0);

        $searcher = new HistoryChangesSearcher();
        $searcher->setMainFilters($type, $primaryId, $referenceType, $referenceId)
            ->setSortOrderProperty(HistoryChanges::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit($limit)
            ->setOffset($offset)
        ;

        if ($userId) {
            if ($isExclude) {
                $searcher->setExcludeUserId($userId);
            }
            else {
                $searcher->setUserId($userId);
            }
        }

        $historyChanges   = $this->historyChangesService->search($searcher)->getItems();
        $decoratorFactory = $this->decoratorFactory;

        $users     = $this->userService->search()->getItems();
        $userOptions = [];
        foreach ($users as $user) {
            $userOptions[$user->getId()] = $user->getViewer()->getDisplayName();
        }
        asort($userOptions);

        return view('pages.admin.history', compact(
            'historyChanges', 'limit', 'offset', 'decoratorFactory', 'userOptions', 'userId', 'isExclude',
        ));
    }
}
