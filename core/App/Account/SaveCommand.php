<?php declare(strict_types=1);

namespace Core\App\Account;

use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Account\AccountService;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private AccountFactory $accountFactory,
        private AccountService $accountService,
        private ExDataService  $exDataService,
        private SaveValidator  $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $id,
        ?string $number,
        bool    $isInvoicing,
        ?int    $size,
        ?string $cadastreNumber,
    ): ?AccountEntity
    {
        $this->validator->validate($id, $number, $size);

        $account = $id
            ? $this->accountService->getById($id)
            : $this->accountFactory->makeDefault();

        if ($account === null) {
            return null;
        }

        $account->setIsVerified(true);
        $account
            ->setNumber($number)
            ->setIsInvoicing($isInvoicing)
            ->setSize($size)
        ;

        $account = $this->accountService->save($account);

        $exData = $this->exDataService->getByTypeAndReferenceId(ExDataTypeEnum::ACCOUNT, $account->getId())
            ? : $this->exDataService->makeDefault(ExDataTypeEnum::ACCOUNT)->setReferenceId($account->getId());

        $exData->setData(
            $account->getExData()
                ->setCadastreNumber($cadastreNumber)
                ->jsonSerialize(),
        );

        $this->exDataService->save($exData);

        return $this->accountService->getById($account->getId());
    }
}
