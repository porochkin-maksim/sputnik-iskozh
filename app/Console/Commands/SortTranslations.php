<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Translations\TranslationService;
use Illuminate\Console\Command;

class SortTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sort-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new TranslationService())->sortTranslations();
    }
}
