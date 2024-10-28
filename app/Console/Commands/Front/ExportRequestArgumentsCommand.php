<?php declare(strict_types=1);

namespace App\Console\Commands\Front;

use Core\Requests\RequestArgumentsEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class ExportRequestArgumentsCommand extends Command
{
    protected const OUTPUT_FILE_PATH = '/js/utils/request-arguments.js';

    protected $signature   = 'front:export-request-arguments-command';
    protected $description = 'Экспортирует названия аргументов в файл для фронта';

    public function handle(): void
    {
        $arguments = [];

        $requestArguments = new ReflectionClass(RequestArgumentsEnum::class);
        $constants        = $requestArguments->getConstants();

        foreach ($constants as $name => $value) {
            $arguments[Str::camel(Str::lower($name))] = $value;
        }

        File::put(resource_path(self::OUTPUT_FILE_PATH), sprintf("export default {\n%s\n}", $this->renderAssocArray($arguments)));
        $this->line("Arguments exported to " . self::OUTPUT_FILE_PATH);
    }

    function renderAssocArray(array $array, int $indents = 1): string
    {
        $spaces        = str_repeat('    ', $indents);
        $values        = [];
        $maxNameLength = 0;

        foreach ($array as $name => $value) {
            if ( ! is_integer($value)) {
                $value = "'{$value}'";
            }

            $values[] = ['name' => $name, 'value' => $value];

            if (strlen($name) > $maxNameLength) {
                $maxNameLength = strlen($name);
            }
        }

        return $spaces . trim(implode(",\n", array_map(fn(array $item) => $spaces . sprintf("%-{$maxNameLength}s: %s", $item['name'], $item['value']), $values)));
    }
}
