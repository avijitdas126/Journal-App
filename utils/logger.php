<?php
// 1. Production Safety: Hide errors from browser, log them internally
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;

// 2. Setup Monolog Channel
$log_file = __DIR__ . '/../logs/app.log';
$log = new Logger('app_logger');

// Set to Warning for production; change to Debug for development
$log->pushHandler(new StreamHandler($log_file, Level::Warning));

// 3. Register Monolog to handle all PHP Errors & Exceptions automatically
$handler = ErrorHandler::register($log);

/**
 * 4. Specialized Helper Functions
 * These make it easier to log without remembering Level constants
 */

function log_info($message, array $context = []) {
    global $log;
    $log->info($message, $context);
}

function log_warn($message, array $context = []) {
    global $log;
    $log->warning($message, $context);
}

function log_error($message, array $context = []) {
    global $log;
    $log->error($message, $context);
}

/**
 * 5. Handle "Fatal" Crashes gracefully
 * This shows a clean message to the user while logging the disaster.
 */
register_shutdown_function(function () use ($log) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $log->critical("FATAL ERROR: " . $error['message'], [
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        
        // Check if we are in a web context before showing HTML
        if (php_sapi_name() !== 'cli') {
            http_response_code(500);
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Something went wrong on our end. The administrators have been notified.</p>";
        }
    }
});
