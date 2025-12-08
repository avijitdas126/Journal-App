<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

$id = $_GET['id']; // article_id

// ---------------------
// GET ARTICLE
// ---------------------
$sql = "SELECT * FROM article WHERE article_id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Safety check
if (!$article) {
    die("<h3>Article not found</h3>");
}

// ---------------------
// GET CATEGORY
// ---------------------
$sql = "SELECT * FROM category WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $article['category']]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// ---------------------
// GET ALL PREVIOUS REVIEWS (teacher + student revisions)
// ---------------------
$sql = "
    SELECT r.*, a.name AS reviewer_name
    FROM reviews r
    LEFT JOIN admins a ON a.admin_id = r.reviewer_id
    WHERE r.article_id = :id
    ORDER BY r.created_at ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "
    SELECT rev.*, s.name AS student_name 
    FROM revisions rev
    LEFT JOIN students s ON s.user_id = rev.author_id
    WHERE rev.article_id = :id
    ORDER BY rev.updated_at ASC;
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-2" style="height:100vh; overflow-y:scroll;">

    <!-- ARTICLE HEADER -->
    <div class="container py-2">
        <div class="row py-2">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2><?php echo htmlspecialchars($article['title']); ?></h2>

                <div>
                    <?php if ($category) { ?>
                        <span class="badge bg-info text-dark">
                            Category: <?php echo htmlspecialchars($category['category']) ?>
                        </span>
                    <?php } ?>

                    <span class="badge bg-secondary">
                        Author ID: <?php echo htmlspecialchars($article['author_id']); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- ARTICLE CONTENT -->
        <div class="row py-2">
            <div class="col-12"
                style="margin:12px;overflow-y:scroll;max-height:400px;border:1px solid #ddd; padding:15px; border-radius:5px; background-color:#f9f9f9;">
                <?php echo $article['content_html']; ?>
            </div>
        </div>

        <!-- REVISION TIMELINE -->
        <h4 class="mt-4">Revision Timeline</h4>

        <div class="row py-2">
            <div class="col-12"
                style="margin:12px;overflow-y:scroll;max-height:400px;border:1px solid #ddd; padding:15px; border-radius:5px; background-color:#f1faff;">

                <?php if (count($reviews) == 0) { ?>
                    <p class="text-muted">No revisions yet.</p>
                <?php } ?>

                <?php foreach ($reviews as $rev) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <span class="badge bg-primary">
                                Reviewer: <?php echo htmlspecialchars($rev['reviewer_name'] ?? "Teacher"); ?>
                            </span>
                            <span class="badge bg-secondary float-end">
                                <?php echo htmlspecialchars($rev['created_at']); ?>
                            </span>

                            <p class="mt-3">
                                <?php echo nl2br(htmlspecialchars($rev['reviewer_text'])); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
                <?php if (count($revisions) == 0) { ?>
                    <p class="text-muted">No revisions yet.</p>
                <?php } ?>

                <?php foreach ($revisions as $rev) { ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <span class="badge bg-info ">
                                Author: <?php echo htmlspecialchars($rev['author_id'] ?? "Student"); ?>
                            </span>
                            <span class="badge bg-secondary float-end">
                                <?php echo htmlspecialchars($rev['updated_at']); ?>
                            </span>

                            <p class="mt-3">
                                <?php echo nl2br(htmlspecialchars($rev['revision_text'])); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- REVIEW SUBMISSION FORM -->
        <div class="row py-2">
            <div class="col-12 mb-5">
                <form action="http://localhost/Journal/views/components/api/submit_review.php" method="POST">
                    <input type="hidden" name="article_id" value="<?php echo $id ?>">
                    <input type="hidden" name="reviewer_id" value="<?php echo $_SESSION['user_id'] ?>">
                    <div class="mb-3">
                        <label for="review-content" class="form-label">Review Content</label>
                        <textarea class="form-control" id="review-content" name="content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="review-marks" class="form-label">Marks</label>
                        <input type="number" class="form-control" id="review-marks" name="marks" required>
                    </div>
                    <div class="mb-3">
                        <label for="review-status" class="form-label">Review Status</label>
                        <select class="form-select" id="review-status" name="status" required>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                            <option value="review">Needs Revision</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>

    </div>
</div>