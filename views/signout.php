

<?php
ini_set('display_errors', 0);
session_start();
// remove all session variables
session_unset();

// destroy the session
session_destroy();
header("Location: login.php");
exit();
?>