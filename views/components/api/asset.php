<?php

$uploadDir = __DIR__ . "/../../../uploads/"; // absolute path
$uploadUrl = "/Journal/uploads/"; // adjust to your public URL
require_once __DIR__ . '/../../../utils/db_conn.php';
$output = ["success" => 0];
session_start();
// --- Check if user is logged in ---
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    $output["error"] = "User not logged in. user_id missing in session.";
    echo json_encode($output);
    exit;
}
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    $fileName = basename($_FILES['image']['name']);
    $ext = explode('.', $fileName)[1];
    $conn = db_conn(
        Env('servername'),
        Env('db'),
        Env('username'),
        Env('password')
    );
    $id = uniqid();
    $newfilename = $id . '.' . $ext;
    $tmpName = $_FILES['image']['tmp_name'];
    // Ensure folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($tmpName, $uploadDir . $newfilename)) {

        $output["success"] = 1;
        $output["file"]["url"] = "http://localhost/Journal/uploads/" . $newfilename; // correct URL for frontend
        $sql = "INSERT INTO `asset` (`alt`, `url`, `author_id`) VALUES ( :alt, :url, :id)";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            ':alt' => $fileName,
            ':url' => $output["file"]["url"],
            ':id' => $userId
        ]);
    }

}

echo json_encode($output);
?>