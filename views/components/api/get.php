<?php 


header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
error_reporting(0); // hide warnings
require_once __DIR__ . '/../../../utils/db_conn.php';

switch ($method) {
    case 'GET':
        $article_id = $_GET['article_id'];
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
            $article = $success->fetchAll(PDO::FETCH_ASSOC)[0];
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Article fetched successfully",
                "article" => $article
            ]);
        } catch (PDOException $e) {
            http_response_code(404);
            echo json_encode(["message" => $e->getMessage()]);
            // echo "Error: " . $e->getMessage();
        } finally {
            db_close($conn);
        }
        break;
        default:
            http_response_code(404);
            echo json_encode(["message" => "Invalid request method"]);
            break;
    }