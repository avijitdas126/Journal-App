<?php

header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
error_reporting(0); // hide warnings
require_once __DIR__ . '/../utils/db_conn.php';
if (!$input) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON"]);
    exit;
}

if ($method == 'POST') {

    $name = $input['name'];
    $username = $input['username'];
    $studentId = $input['studentId'];
    $college = $input['college'];
    $role = $input['role'];
    $password = $input['password'];

    $hash = password_hash($password, PASSWORD_BCRYPT);
    try {
        // Connect to DB
        $conn = db_conn(
            Env('servername'),
            Env('db'),
            Env('username'),
            Env('password')
        );

        // Prepared query (safe)
        $sql = "INSERT INTO `user` 
        (`name`, `username`, `student_id`, `role`, `college_name`, `password`, `avatar_url`, `createAt`, `updateAt`)
        VALUES (:name, :username, :student_id, :role, :college, :password, '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";

        $stmt = $conn->prepare($sql);

        $success = $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':student_id' => $studentId,
            ':role' => $role,
            ':college' => $college,
            ':password' => $hash
        ]);
        http_response_code(200);
        echo json_encode(["message" => "User added successfully"]);
    } catch (PDOException $e) {
        http_response_code(404);
        echo json_encode(["message" => $e->getMessage()]);
        // echo "Error: " . $e->getMessage();
    } finally {
        db_close($conn);
    }

}
?>