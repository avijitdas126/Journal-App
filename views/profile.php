<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../utils/db_conn.php';
require_once __DIR__ . '/../utils/base.php';

$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
session_start();

$author_username = $_GET['username'] ?? '';

// 1. Fetch Author Data (Logic simplified for readability)
$author = null;
$author_type = '';

$stmt = $conn->prepare("SELECT *, 'student' as type FROM `students` WHERE `username` = ?");
$stmt->execute([$author_username]);
$author = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$author) {
    $stmt = $conn->prepare("SELECT *, 'admin' as type FROM `admins` WHERE `username` = ?");
    $stmt->execute([$author_username]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$author) {
    die("User not found.");
}

$author_type = $author['type'];
$author_id = ($author_type === 'student') ? $author['user_id'] : $author['admin_id'];

// 2. Fetch Author's Articles
$artStmt = $conn->prepare("SELECT * FROM article WHERE author_id = ? AND author_type = ? AND status = 'published' ORDER BY created_at DESC");
$artStmt->execute([$author_id, $author_type]);
$articles = $artStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Department Name
$dept_id = $author['department_id'];
$stm = $conn->prepare("SELECT name FROM departments WHERE department_id = ?");
$stm->execute([$dept_id]);
$dept_name = $stm->fetchColumn() ?: "General";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon">
  <link rel="icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title><?php echo htmlspecialchars($author['name']); ?> | Profile</title>
    <style>
        :root {
            --primary-grad: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        
        /* Profile Section */
        .profile-card { border: none; border-radius: 1.5rem; overflow: hidden; background: #fff; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .profile-header { background: var(--primary-grad); padding: 3rem 2rem; color: white; text-align: center; }
        .avatar-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); }
        
        /* Blog Section */
        .section-title { font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; position: relative; }
        .article-card { 
            border: none; border-radius: 1rem; transition: transform 0.2s; 
            height: 100%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .article-card:hover { transform: translateY(-5px); }
        .status-badge { font-size: 0.7rem; text-transform: uppercase; padding: 0.25rem 0.6rem; border-radius: 20px; background: #e0e7ff; color: #4338ca; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="profile-card">
                <div class="profile-header">
                    <img src="<?php echo !empty($author['avatar_url']) ? $author['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($author['name']) . "&background=random"; ?>" class="avatar-img" alt="Profile">
                    <h3 class="mt-3 mb-0"><?php echo htmlspecialchars($author['name']); ?></h3>
                    <p class="opacity-75">@<?php echo htmlspecialchars($author['username']); ?></p>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">COLLEGE</label>
                        <div class="fw-medium"><?php echo htmlspecialchars($author['college_name']); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">DEPARTMENT</label>
                        <div class="fw-medium"><?php echo htmlspecialchars($dept_name); ?></div>
                    </div>
                    <div>
                        <label class="text-muted small fw-bold">EMAIL</label>
                        <div class="fw-medium"><?php echo htmlspecialchars($author['email']??"Not provided"); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <h4 class="section-title">Published Articles (<?php echo count($articles); ?>)</h4>
            
            <?php if (empty($articles)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <p class="text-muted">No articles published yet.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($articles as $article): ?>
                        <div class="col-md-6">
                            <div class="card article-card p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="status-badge">Article</span>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($article['updated_at'])); ?></small>
                                </div>
                                <h5 class="card-title fw-bold">
                                    <a href="../main.php?page=article&slug=<?php echo $article['slug']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h5>
                                <p class="text-muted small">
                                    <?php echo htmlspecialchars(substr($article['description'], 0, 100)) . '...'; ?>
                                </p>
                                <a href="../main.php?page=article&slug=<?php echo $article['slug']; ?>" class="btn btn-sm btn-outline-primary mt-auto w-100">Read More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>