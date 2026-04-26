<?php declare(strict_types=1);

namespace Core\Domains\Proposal\Services;

use Core\Domains\Enums\Emails;
use Core\Domains\Proposal\Jobs\ProposalCreatedJob;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use App\Services\Files\Collections\TmpFiles;
use App\Services\Files\Models\TmpFile;
use App\Services\Files\Services\TmpFileService;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

readonly class ProposalService
{
    public function __construct(
        private TmpFileService $tmpFileService,
    )
    {
    }

    /**
     * @param UploadedFile[] $files
     */
    public function notify(string $fullText, string $name, array $files): void
    {
        $attachFiles = new TmpFiles();
        foreach ($files as $file) {
            $attachFiles->add(new TmpFile(
                $file->getName(),
                $this->tmpFileService->createTmpFile($file->getExtension(), $file->getContent()),
                $file->getExtension(),
            ));
        }

        $proposalFilePath = $this->tmpFileService->createTmpFile('pdf');

        $pdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $pdf->SetProtection(['copy', 'print'], '', md5(random_bytes(10)));
        $pdf->WriteHTML(nl2br($fullText));
        $pdf->Output($proposalFilePath, Destination::FILE);

        dispatch(new ProposalCreatedJob(
            $proposalFilePath,
            $name,
            Emails::pressAddresses(),
            $attachFiles,
        ));
    }
}
