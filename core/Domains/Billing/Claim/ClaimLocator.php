<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

use Core\Domains\Billing\Claim\Factories\ClaimFactory;
use Core\Domains\Billing\Claim\Repositories\ClaimRepository;
use Core\Domains\Billing\Claim\Services\ClaimService;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

abstract class ClaimLocator
{
    private static ClaimService    $claimService;
    private static ClaimFactory    $claimFactory;
    private static ClaimRepository $claimRepository;

    public static function ClaimService(): ClaimService
    {
        if ( ! isset(self::$claimService)) {
            self::$claimService = new ClaimService(
                self::ClaimFactory(),
                self::ClaimRepository(),
                HistoryChangesLocator::HistoryChangesService(),
            );
        }

        return self::$claimService;
    }

    public static function ClaimFactory(): ClaimFactory
    {
        if ( ! isset(self::$claimFactory)) {
            self::$claimFactory = new ClaimFactory();
        }

        return self::$claimFactory;
    }

    public static function ClaimRepository(): ClaimRepository
    {
        if ( ! isset(self::$claimRepository)) {
            self::$claimRepository = new ClaimRepository();
        }

        return self::$claimRepository;
    }
}
