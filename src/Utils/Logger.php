<?php

declare(strict_types=1);

namespace App\Utils;

final class Logger
{
    public static function log(string $message): void
    {
        $log = "####### ".date('Y-m-d H:i:s'). " #######\n";
        $log .= $message."\n";
        $log .= str_repeat("_", 100) . str_repeat("\n", 3);
        file_put_contents(__DIR__ . '/deploy-log.txt', $log, FILE_APPEND);
    }
}
