<?php declare(strict_types=1);

namespace Core\App\Counter;

use Carbon\Carbon;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class SaveAdminCounterCommand
{
    public function __construct(
        private CounterService $counterService,
        private CounterFactory $counterFactory,
        private FileService    $fileService,
        private SaveAdminCounterValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(
        int           $accountId,
        int           $counterId,
        bool          $isInvoicing,
        int           $increment,
        string        $number,
        ?Carbon       $expireAt,
        ?UploadedFile $passportFile,
    ): void
    {
        $this->validator->validate($counterId ?: null, $number);

        $counter = $this->counterService->getById($counterId);

        if ($counter !== null && $counter->getAccountId() !== $accountId) {
            abort(404);
        }

        DB::transaction(function () use (
            $accountId,
            $counter,
            $isInvoicing,
            $increment,
            $number,
            $expireAt,
            $passportFile
        ) {
            $counter = $counter ? : $this->counterFactory->makeDefault()->setAccountId($accountId);

            $counter
                ->setIsInvoicing($isInvoicing)
                ->setIncrement($increment)
                ->setNumber($number)
                ->setExpireAt($expireAt)
            ;

            $counter = $this->counterService->save($counter);

            if ($passportFile !== null) {
                $this->fileService->storePassportFile($passportFile, $counter->getId());
            }
        });
    }
}
