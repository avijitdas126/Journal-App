<?php

require_once __DIR__ . '/../../../utils/db_conn.php';
session_start();

$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$article_id      = $_POST['article_id'];
$student_id      = $_POST['student_id'];
$new_title       = $_POST['article_title'];
$new_category    = $_POST['catagory'];
$content_json    = $_POST['content_json'];
$content_html    = $_POST['content_html'];
$student_message = $_POST['student_message'] ?? "";

// --------------------------------------------
// 1. Verify student owns the article
// --------------------------------------------
$sql = "SELECT author_id FROM article WHERE article_id = :id LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $article_id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article || $article['author_id'] != $student_id) {
    die("Unauthorized attempt!");
}

// --------------------------------------------
// 2. Insert new revision entry (timeline)
// --------------------------------------------
$revision_sql = "
    INSERT INTO revisions (article_id, author_id, revision_text, updated_at)
    VALUES (:article_id, :author_id, :text, CURRENT_TIMESTAMP)
";

$stm = $conn->prepare($revision_sql);
$stm->execute([
    ':article_id' => $article_id,
    ':author_id' => $student_id,
    ':text'       => $student_message
]);

// --------------------------------------------
// 3. Update actual article
// --------------------------------------------
$update_sql = "
    UPDATE article 
    SET 
        title = :title,
        category = :category,
        content_json = :json,
        content_html = :html,
        status = 'review',
        updated_at = CURRENT_TIMESTAMP
    WHERE article_id = :id
";

$stmt = $conn->prepare($update_sql);
$stmt->execute([
    ':title'    => $new_title,
    ':category' => $new_category,
    ':json'     => $content_json,
    ':html'     => $content_html,
    ':id'       => $article_id
]);

// --------------------------------------------
// 4. Redirect back
// --------------------------------------------
header("Location: http://localhost/Journal/?page=my_articles&msg=resubmitted_successfully");
exit;

?>
