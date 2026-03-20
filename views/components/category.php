<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../../utils/db_conn.php';

$id = $_SESSION['user_id'];
$sql = "SELECT A.article_id,
    A.title,
    A.description,
    A.slug,
    A.submitted_at,
    A.category AS category_id,
    C.category,
    -- author name (student or admin)
    COALESCE(s.name, ad.name) AS author_name,
    A.author_type FROM article AS A JOIN category AS C ON C.id=A.category 
LEFT JOIN students s 
    ON A.author_id = s.user_id 
   AND A.author_type = 'student'
LEFT JOIN admins ad 
    ON A.author_id = ad.admin_id 
   AND A.author_type IN ('admin', 'teacher') 
   WHERE A.status='published' AND C.slug=:slug
   ORDER BY A.submitted_at DESC;
   ";
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        $slug = trim($_GET['slug']);
        $stmt = $conn->prepare($sql);
        $stmt->execute([':slug' => $slug ]);
        $artic= $stmt->fetchAll(PDO::FETCH_ASSOC);
       
}
?>


<head>
    <title>
       <?php 
       echo ucfirst($slug).' :';
       ?> Recent Published Articles
    </title>
</head>
<div class="container" style="height: 100vh; padding-top:20px;">
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h1>
                Published Articles
            </h1>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-12">
            <?php if(count($artic) == 0){ ?>
                <p>No articles have been published yet.</p>
            <?php } ?>
            <div id="articles-page-1" class="row g-4 article-page">
            <?php foreach($artic as $article){ 
                ?>
            
           <a href="?page=article&slug=<?php echo $article['slug']; ?>" style="text-decoration: none; color: inherit;"> <div style="overflow-x:auto; width:100%">
                <div>
                <div class="card article-card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p class="text-muted small">✍️ <?php echo htmlspecialchars($article['author_name']); ?> · 📅 <?php echo date('d M', strtotime($article['submitted_at'])); ?></p>
                        <p class="card-text mt-2"><?php echo htmlspecialchars($article['description']); ?></p>
                    </div>
                </div>
               
            </div>
            </div></a>
             <?php }?>
        </div>
    </div>
    </div>
</div>



