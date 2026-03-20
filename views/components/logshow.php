<?php
require_once __DIR__ . '/../../utils/logger.php';
ini_set('display_errors', 0);


$logFile = __DIR__ . '/../../logs/app.log';

if (file_exists($logFile)) {
    $file_handle = fopen($logFile, "r") or log_warn("Unable to open file!");
    // $logContent = file_get_contents($logFile);
    // echo "<pre>" . htmlspecialchars($logContent) . "</pre>";
    $file_handle = fopen($logFile, "r") or log_warn("Unable to open file!");
    echo "<div class='container p-3'>";
    echo "<h2>Application Log</h2>";
    echo "<p>Showing contents of: <strong>" . htmlspecialchars($logFile) . "</strong></p>";
    echo "<div style='background-color: #f8f9fa; padding: 10px; border: 1px solid #ddd; height: 800px; overflow-y: scroll; font-family: monospace;'>";
    echo "<pre>";
    // Output one line until end-of-file
    $lineno = 1;
    while (!feof($file_handle)) {
        $line = fgets($file_handle);
        // Optional: use rtrim() to remove the newline character from the string
        // $line = rtrim($line);
        echo $lineno . ') ' . $line . "<br>";
        $lineno++;
    }
    fclose($file_handle);
    echo "</pre>";
    echo "</div>";
    echo "</div>";

} else {
    log_warn("Log file not found.");
}

?>