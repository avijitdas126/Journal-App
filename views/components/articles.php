<?php 

require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$sql="SELECT 
    a.article_id,
    a.title,
    a.description,
    s.username,
    s.avatar_url,
    a.slug,
    a.submitted_at,
    c.category AS category_name,

    -- author name (student or admin)
    COALESCE(s.name, ad.name) AS author_name,
    a.author_type

FROM article a
LEFT JOIN category c 
    ON a.category = c.id

LEFT JOIN students s 
    ON a.author_id = s.user_id 
   AND a.author_type = 'student'

LEFT JOIN admins ad 
    ON a.author_id = ad.admin_id 
   AND a.author_type IN ('admin', 'teacher')

WHERE a.status = 'published'
ORDER BY a.submitted_at DESC;
";
$articles=$conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>
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
            <?php if(count($articles) == 0){ ?>
                <p>No articles have been published yet.</p>
            <?php } ?>
            <div id="articles-page-1" class="row g-4 article-page">
            <?php foreach($articles as $article){ ?>
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