<?php 

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
error_reporting(0);
require_once __DIR__ . '/../../../utils/db_conn.php';

switch ($method) {

    case 'GET':

        $article_id = intval($_GET['article_id']);

        try {
            $conn = db_conn(
                Env('servername'),
                Env('db'),
                Env('username'),
                Env('password')
            );

            $stmt = $conn->prepare("SELECT * FROM `article` WHERE `article_id` = ?");
            $stmt->execute([$article_id]);

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$article) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Article not found"]);
                exit;
            }

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Article fetched successfully",
                "article" => $article  // Return a SINGLE object
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } finally {
            db_close($conn);
        }

        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}
