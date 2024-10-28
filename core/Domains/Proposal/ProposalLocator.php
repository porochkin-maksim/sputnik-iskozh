<?php declare(strict_types=1);

namespace Core\Domains\Proposal;

use Core\Domains\Proposal\Services\ProposalService;
use Core\Services\Files\FileLocator;

abstract class ProposalLocator
{
    private static ProposalService $proposalService;

    public static function proposalService(): ProposalService
    {
        if ( ! isset(self::$proposalService)) {
            self::$proposalService = new ProposalService(
                FileLocator::TmpFileService(),
            );
        }

        return self::$proposalService;
    }
}
