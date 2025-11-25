<?php

require_once 'utils/load_env.php';
load_env();

function db_conn($servername, $db, $username, $password): PDO|null
{
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo 'Connection successfully';
        return $conn;
    } catch (PDOException $e) {
        echo "Connection failed" . $e->getMessage();
        return null;
    }

}
function table_exists(PDO $conn, string $table): bool
{
    $database = Env('db');

    $query = "SELECT TABLE_NAME 
              FROM INFORMATION_SCHEMA.TABLES 
              WHERE TABLE_SCHEMA = '$database' 
              AND TABLE_NAME = '$table'";

    $success = $conn->query($query);

    return $success !== false;
}

function db_setup(PDO &$conn): void
{
    $isExit = table_exists($conn, 'user');
    if ($isExit) {
        $sql = "CREATE TABLE `user` (`id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL , `student_id` VARCHAR(50) NOT NULL , `college_name` VARCHAR(50) NOT NULL , `password` VARCHAR(255) NOT NULL , createAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updateAt` DATETIME NULL DEFAULT NULL , `role` ENUM('student','teacher','developer','admin') NOT NULL , PRIMARY KEY (`id`), UNIQUE (`student_id`));";
        $success = $conn->exec(statement: $sql);
        if (!$success) {
            echo nl2br(string: "\n Setup is successfully");
        } else {
            die(nl2br(string: "\n Setup is unsuccessfully"));
        }
    }
}

function db_close(&$conn): void
{
    $conn = null;
    echo nl2br("\nConnection is successfully closed");
}


