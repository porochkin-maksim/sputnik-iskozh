<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class ErrorLogsController extends Controller
{
    public function index()
    {
        $logsPath = storage_path('logs/errors');
        $errorLogs = collect(File::files($logsPath))
            ->filter(fn($file) => str_contains($file->getFilename(), 'error'))
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
                'path' => $file->getPathname(),
            ])
            ->sortByDesc('modified')
            ->values();

        return view('admin.error-logs.index', [
            'logs' => $errorLogs,
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function show(string $filename)
    {
        $entries = $this->getValues($filename);

        return view('admin.error-logs.show', [
            'filename' => $filename,
            'entries' => $entries,
        ]);
    }

    /**
     * @throws FileNotFoundException
     */
    public function details(string $filename, int $index)
    {
        $entries = $this->getValues($filename);

        if (!isset($entries[$index])) {
            abort(404);
        }

        return view('admin.error-logs.details', [
            'filename' => $filename,
            'entry' => $entries[$index],
            'index' => $index,
        ]);
    }

    /**
     * @param string $filename
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    public function getValues(string $filename)
    {
        $logPath = storage_path('logs/errors/' . $filename);

        if ( ! File::exists($logPath)) {
            abort(404);
        }

        $content = File::get($logPath);
        $entries = collect(explode("\n", $content))
            ->filter()
            ->map(function ($entry) {
                $decoded = json_decode($entry);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_object($decoded)) {
                    return null;
                }

                return $decoded;
            })
            ->filter()
            ->values()
        ;

        return $entries;
    }
} 