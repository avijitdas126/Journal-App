<?php

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
error_reporting(0); // hide warnings
require_once __DIR__ . '/../../../utils/db_conn.php';
if (!$input) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}
switch ($method) {
    case 'POST':
        $article_id = $input['article_id'];
        $author_id = $input['author_id'];
        $author_type = $input['author_type'];
        $title = $input['title'];
        $status = $input['status'];
        $slug = $input['slug'];
        $content_json = $input['content_json'];
        $content_html = $input['content_html'];
        try {
            // Connect to DB
            $conn = db_conn(
                Env('servername'),
                Env('db'),
                Env('username'),
                Env('password')
            );
            $sql = "SELECT * FROM `article` WHERE `article_id` = $article_id;";
            $success = $conn->query($sql);
            
            if (count($success->fetchAll())) {
                $sql = "UPDATE `article` 
                SET `title` = :title, 
                `slug` = :slug, 
                `content_json` = :content_json,
                `status` = :status,
                `content_html` = :content_html, 
                `submitted_at` = NULL, `published_at` = NULL, updated_at = CURRENT_TIMESTAMP, `deleted_at` = NULL 
                 WHERE `article`.`article_id` = :article_id;";
                $stmt = $conn->prepare($sql);
                $success = $stmt->execute([
                    ':article_id' => $article_id,
                    ':title' => $title,
                    ':slug' => $slug,
                    ':status' => $status,
                    ':content_json' => $content_json,
                    ':content_html' => $content_html
                ]);
                 http_response_code(200);
                  echo json_encode([
                    "status" => "success",
                    "message" => "Article updated successfully",
                    "article_id" => $article_id
                ]);
            } else {
                $sql = "
         INSERT INTO `article` 
         ( `article_id`,`author_id`, `author_type`, `title`, `slug`, `status`, `content_json`, `content_html`, `submitted_at`, `published_at`, `created_at`, `updated_at`, `deleted_at`)
          VALUES (
            :article_id,
            :id, 
            :type,
            :title,
            :slug,
            :status,
            :content_json,
            :content_html,
            NULL, 
            NULL, 
            CURRENT_TIMESTAMP, 
            CURRENT_TIMESTAMP, 
            NULL
            );
         ";
                $stmt = $conn->prepare($sql);

                $success = $stmt->execute([
                    ':article_id' => $article_id,
                    ':id' => $author_id,
                    ':type' => $author_type,
                    ':title' => $title,
                    ':slug' => $slug,
                    ':status' => $status,
                    ':content_json' => $content_json,
                    ':content_html' => $content_html
                ]);
                http_response_code(200);
                $article_id = $conn->lastInsertId();

                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Article added successfully",
                    "article_id" => $article_id
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(404);
            echo json_encode(["message" => $e->getMessage()]);
            // echo "Error: " . $e->getMessage();
        } finally {
            db_close($conn);
        }

        break;

    default:
        # code...
        break;
}