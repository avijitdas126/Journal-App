<?php 
error_reporting(E_ALL);
ini_set('display_errors', value: 1);
function log_message($level, $message) {
    $log_file = __DIR__ . '/../logs/app.log';
    $date = date('Y-m-d H:i:s');
    $formatted_message = "[$date] [$level] $message" . PHP_EOL;
    error_log($formatted_message, 3, $log_file);
    
}
set_error_handler("log_message");

function log_error($message) {
    log_message('ERROR', $message);
}