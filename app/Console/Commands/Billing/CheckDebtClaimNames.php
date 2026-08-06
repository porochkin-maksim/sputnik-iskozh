<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\App\Billing\Claim\CheckDebtClaimNamesCommand;
use Illuminate\Console\Command;

class CheckDebtClaimNames extends Command
{
    public function __construct(
        private readonly CheckDebtClaimNamesCommand $command,
    )
    {
        parent::__construct();
    }

    protected $signature   = 'billing:claims:check-debt-names
                              {--fix : Применить исправления названий (без флага — только проверка)}';
    protected $description = 'Проверяет и исправляет названия долговых услуг (claims типа Долг)';

    public function handle(): void
    {
        $fix = (bool) $this->option('fix');

        $result = $this->command->execute($fix);

        $this->info("Проверено услуг: {$result['checked']}");

        if ($result['broken'] === 0) {
            $this->info('Битых названий не найдено.');

            return;
        }

        $this->warn("Найдено битых названий: {$result['broken']}");

        foreach ($result['items'] as $item) {
            $this->line(sprintf(
                '   #%d: "%s" -> "%s"',
                $item['id'],
                $item['old'] ?? '(пусто)',
                $item['new'],
            ));
        }

        if ( ! $fix) {
            $this->warn('Запустите с --fix для применения исправлений.');

            return;
        }

        $this->info("Исправлено: {$result['fixed']}");
        $this->info('Готово.');
    }
}
