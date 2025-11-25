<?php

require_once 'utils/db_conn.php';


$conn=db_conn(Env('servername'),Env('db'),Env('username'),Env('password'));
db_setup($conn);
db_close($conn);

function db_setup(PDO &$conn): void
{
    $isExit = table_exists($conn, 'user');
    
    if (!$isExit) {
        $sql = "CREATE TABLE `user` (
        `id` INT NOT NULL AUTO_INCREMENT , 
        `name` VARCHAR(50) NOT NULL,
        `username` VARCHAR(50) NOT NULL,
        `student_id` VARCHAR(50) DEFAULT NULL , 
        `role` ENUM('student','teacher','developer','admin') NOT NULL, 
        `college_name` VARCHAR(50) NOT NULL , 
        `password` VARCHAR(255) NOT NULL,
        `avatar_url` VARCHAR(225) NOT NULL, 
        `createAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        `updateAt` DATETIME NULL DEFAULT NULL  ,
        PRIMARY KEY (`id`),
        UNIQUE (`username`)
        );";
        $success = $conn->exec(statement: $sql);
        if (!$success) {
            echo nl2br(string: "\nSetup is successfully");
        } else {
            die(nl2br(string: "\nSetup is unsuccessfully"));
        }
    }
}




