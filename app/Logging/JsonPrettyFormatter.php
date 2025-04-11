<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\LogRecord;

class JsonPrettyFormatter extends JsonFormatter
{
    public function __construct()
    {
        parent::__construct(
            self::BATCH_MODE_JSON,
            true,
            true,
            true
        );
    }

    public function format(LogRecord $record): string
    {
        $json = parent::format($record);
        return json_encode(
            json_decode($json),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) . "\n";
    }
} 