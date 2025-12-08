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
        // print_r($_POST);
        $article_id = $_POST['article_id'];
        $reviewer_id = $_POST['reviewer_id'];
        $content = $_POST['content'];
        $marks = $_POST['marks'];
        $status = $_POST['status'];
        try {
            $sql = "INSERT INTO `reviews` 
            (`article_id`, `reviewer_id`, `reviewer_text`, `marks`, `created_at`) 
            VALUES 
            (:article_id, :reviewer_id, :content, :marks, CURRENT_TIMESTAMP);";
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute([
                ':article_id' => $article_id,
                ':reviewer_id' => $reviewer_id,
                ':content' => $content,
                ':marks' => $marks,
            ]);
            // Update article status
            if ($status == 'approved') {
                $sql = "UPDATE `article` SET `status` = 'published' WHERE `article_id` = :article_id";
                $stmt = $conn->prepare($sql);
                $success = $stmt->execute([
                    ':article_id' => $article_id
                ]);
            } else {
                $sql = "UPDATE `article` SET `status` = :status WHERE `article_id` = :article_id";
                $stmt = $conn->prepare($sql);
                $success = $stmt->execute([
                    ':status' => $status,
                    ':article_id' => $article_id
                ]);
            }
            header("Location: http://localhost/Journal/views/dashboard.php?page=reviews");
        } catch (PDOException $e) {
            // echo "Error: " . $e->getMessage();
        } finally {
            db_close($conn);
        }
        break;

    default:
        # code...
        break;
}