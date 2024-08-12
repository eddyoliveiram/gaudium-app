<?php

class Logger {
    public static function logMessage($level, $message) {
        $date = date('Y-m-d H:i:s');
        $logEntry = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents('app.log', $logEntry, FILE_APPEND);
    }
}
