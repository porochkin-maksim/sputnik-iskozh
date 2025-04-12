<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    public function status()
    {
        $output = [];
        exec('ps aux | grep "queue:work"', $output);

        $workers = [];
        foreach ($output as $line) {
            if (str_contains($line, 'artisan queue:work')) {
                // Извлекаем PID из строки
                preg_match('/^\S+\s+(\d+)/', $line, $matches);
                $pid = $matches[1] ?? null;

                // Извлекаем очередь из строки
                preg_match('/--queue=([^ ]+)/', $line, $queueMatches);
                $queue = $queueMatches[1] ?? 'default';

                // Извлекаем время работы
                preg_match('/\d+:\d+:\d+/', $line, $timeMatches);
                $uptime = $timeMatches[0] ?? '00:00:00';

                // Извлекаем использование CPU
                preg_match('/\d+\.\d+/', $line, $cpuMatches);
                $cpu = $cpuMatches[0] ?? '0.0';

                if ($pid) {
                    $workers[] = [
                        'pid' => $pid,
                        'queue' => $queue,
                        'uptime' => $uptime,
                        'cpu' => $cpu
                    ];
                }
            }
        }

        return response()->json([
            'workers' => $workers
        ]);
    }

    public function start(Request $request)
    {
        $queue   = $request->input('queue', 'default');
        $tries   = $request->input('tries', 3);
        $timeout = $request->input('timeout', 60);

        try {
            // Сначала останавливаем существующий воркер для этой очереди
            $this->stopWorker($queue);

            // Запускаем новый воркер
            Artisan::call('queue:worker:start', [
                'queue' => $queue,
                '--tries' => $tries,
                '--timeout' => $timeout
            ]);

            return response()->json(['success' => true, 'message' => 'Воркер запущен']);
        }
        catch (\Exception $e) {
            Log::error('Ошибка запуска воркера: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function stop(Request $request)
    {
        $queue = $request->input('queue', 'default');

        try {
            $this->stopWorker($queue);

            return response()->json(['success' => true, 'message' => 'Воркер остановлен']);
        }
        catch (\Exception $e) {
            Log::error('Ошибка остановки воркера: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function clear(Request $request)
    {
        $queue = $request->input('queue', 'default');

        try {
            Artisan::call('queue:clear', ['--queue' => $queue]);

            return response()->json(['success' => true, 'message' => 'Очередь очищена']);
        }
        catch (\Exception $e) {
            Log::error('Ошибка очистки очереди: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function stopWorker(string $queue): void
    {
        // Находим PID воркера для указанной очереди
        $output = [];
        exec('ps aux | grep "queue:work.*--queue=' . $queue . '"', $output);

        foreach ($output as $line) {
            if (str_contains($line, 'artisan queue:work')) {
                preg_match('/^\S+\s+(\d+)/', $line, $matches);
                if (isset($matches[1])) {
                    // Останавливаем процесс
                    exec('kill -9 ' . $matches[1]);
                }
            }
        }
    }
} 