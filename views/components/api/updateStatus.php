<?php
require_once __DIR__ . '/../../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(
    Env('servername'),
    Env('db'),
    Env('username'),
    Env('password')
);
switch ($method) {
    case 'POST':
        $author_id = $_POST['id'];
        $article_id = $_POST['article_id'];
        $status = $_POST['status'];
        try {
            // Connect to DB
            $conn = db_conn(
                Env('servername'),
                Env('db'),
                Env('username'),
                Env('password')
            );

            $sql = "SELECT * FROM `article` WHERE `article_id` = $article_id AND `author_id` =$author_id;";
            $success = $conn->query($sql);

            if (count($success->fetchAll())) {
                $sql = "UPDATE `article` 
                SET `status` = :status,
                `submitted_at` = CURRENT_TIMESTAMP
                 WHERE `article`.`article_id` = :article_id AND `article`.`author_id` = :author_id;";
                $stmt = $conn->prepare($sql);
                $success = $stmt->execute([
                    ':status' => $status,
                    ':article_id' => $article_id,
                    ':author_id' => $author_id
                ]);
                header("Location: http://localhost/Journal/views/dashboard.php?page=article");
            }
        } catch (PDOException $e) {
            // echo "Error: " . $e->getMessage();
        } finally {
            db_close($conn);
        }
        break;

}