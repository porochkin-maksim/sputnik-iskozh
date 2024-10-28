<?php declare(strict_types=1);

namespace Core\Domains\Proposal\Services;

use Core\Domains\Proposal\Jobs\ProposalCreatedJob;
use Core\Domains\Proposal\Requests\CreateRequest;
use Core\Notifications\Email\Emails;
use Core\Services\Files\Collections\TmpFiles;
use Core\Services\Files\Models\TmpFile;
use Core\Services\Files\Services\TmpFileService;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

readonly class ProposalService
{
    public function __construct(
        private TmpFileService $tmpFileService,
    )
    {
    }

    public function notify(CreateRequest $request): void
    {
        $attachFiles = new TmpFiles();
        foreach ($request->allFiles() as $file) {
            $attachFiles->add(new TmpFile(
                $file->getClientOriginalName(),
                $this->tmpFileService->createTmpFile($file->getClientOriginalExtension(), $file->getContent()),
                $file->getClientOriginalExtension(),
            ));
        }

        $proposalFilePath = $this->tmpFileService->createTmpFile('pdf');

        $pdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetProtection(['copy', 'print'], '', md5(random_bytes(10)));
        $pdf->WriteHTML(nl2br($request->getFullText()));
        $pdf->Output($proposalFilePath, Destination::FILE);

        dispatch(new ProposalCreatedJob(
            $proposalFilePath,
            $request->getName(),
            Emails::pressAddresses(),
            $attachFiles,
        ));
    }
}
