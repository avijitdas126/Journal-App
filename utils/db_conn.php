<?php 

require_once __DIR__ . '/load_env.php';
require_once __DIR__ . '/logger.php';
load_env();
function db_conn($servername, $db, $username, $password): PDO|null
{
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Connection failed " . $e->getMessage();
        return null;
    }

}

function table_exists(PDO $conn, string $table): bool
{
    $database = Env('db');

    $query = "SELECT COUNT(*) AS cnt
              FROM INFORMATION_SCHEMA.TABLES
              WHERE TABLE_SCHEMA = :db
              AND TABLE_NAME = :table";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':db'    => $database,
        ':table' => $table
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row === false) {
        return false;
    }
    
    return $row['cnt'] != 0;
}



function db_close(&$conn): void
{
    $conn = null;
}