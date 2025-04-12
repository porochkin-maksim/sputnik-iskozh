<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCache extends Command
{
    protected $signature   = 'cache:clear-all';
    protected $description = 'Clear all Laravel caches';

    public function handle(): void
    {
        $this->info('Clearing all caches...');

        // Очистка кеша конфигурации
        Artisan::call('config:clear');
        $this->info('✓ Configuration cache cleared');

        // Очистка кеша роутов
        Artisan::call('route:clear');
        $this->info('✓ Route cache cleared');

        // Очистка кеша представлений
        Artisan::call('view:clear');
        $this->info('✓ View cache cleared');

        // Очистка кеша приложения
        Artisan::call('cache:clear');
        $this->info('✓ Application cache cleared');

        // Очистка кеша событий
        Artisan::call('event:clear');
        $this->info('✓ Event cache cleared');

        // Очистка кеша компилированных файлов
        Artisan::call('clear-compiled');
        $this->info('✓ Compiled files cleared');

        // Очистка кеша автозагрузки
        Artisan::call('optimize:clear');
        $this->info('✓ Optimized files cleared');

        // Очистка кеша планировщика
        Artisan::call('schedule:clear-cache');
        $this->info('✓ Schedule cache cleared');

        // Очистка кеша очередей
        Artisan::call('queue:restart');
        $this->info('✓ Queue restarted');

        $this->newLine();
        $this->info('All caches have been cleared successfully!');
    }
} 