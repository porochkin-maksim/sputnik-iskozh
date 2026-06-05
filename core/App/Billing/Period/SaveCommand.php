<?php declare(strict_types=1);

namespace Core\App\Billing\Period;

use Carbon\Carbon;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodFactory;
use Core\Domains\Billing\Period\PeriodService;

readonly class SaveCommand
{
    public function __construct(
        private PeriodFactory $periodFactory,
        private PeriodService $periodService,
        private SaveValidator $validator,
    )
    {
    }

    public function execute(
        ?int $id,
        ?string $name,
        ?Carbon $startAt,
        ?Carbon $endAt,
        bool $isClosed,
    ): ?PeriodEntity
    {
        $this->validator->validate($name, $startAt, $endAt);

        $period = $id
            ? $this->periodService->getById($id)
            : $this->periodFactory->makeDefault();

        if ($period === null) {
            return null;
        }

        $period
            ->setName($name)
            ->setStartAt($startAt)
            ->setEndAt($endAt)
            ->setIsClosed($isClosed);

        return $this->periodService->save($period);
    }
}
