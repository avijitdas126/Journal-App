<?php 

require_once __DIR__ . '/../../utils/db_conn.php';
$id=$_GET['id'];
$sql="SELECT * FROM `article` WHERE `article_id` = '$id';";
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$article=$conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);  
$article=$article[0];
$cat=$article['category'];
$sql = "SELECT * FROM `category` WHERE `id` = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $cat]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="container-fluid py-2" style="height:100vh; overflow-y:scroll;">
<div class="container py-2">
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h2> <?php echo htmlspecialchars($article['title']); ?></h2>
            <div>
                <?php if($category){ ?>
                <span class="badge bg-info text-dark">Category: <?php echo htmlspecialchars($category['category']) ?></span>
                <?php } ?>
                <span class="badge bg-secondary">Author ID: <?php echo htmlspecialchars($article['author_id']); ?></span>
            </div>
            
    </div>
    <div class="row py-2">
                <div class="col-12" style="margin:12px;overflow-y:scroll;max-height:400px;border:1px solid #ddd; padding:15px; border-radius:5px; background-color:#f9f9f9;">
                <?php echo $article['content_html']; ?>    

            </div>
        </div>
</div>
</div>
<div class="container py-2">
<div class="row py-2">
    <div class="col-12 mb-5">
        <button type="button" class="btn btn-secondary mb-3" onclick="window.location.href='?page=reviews'">
            &larr; Back to Reviews
        </button>
        <h2>Add Review for Article ID: <?php echo $id ?></h2>
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