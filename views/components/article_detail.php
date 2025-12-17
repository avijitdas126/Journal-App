<?php 
require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$slug = $_GET['slug'] ?? '';
$sql="SELECT 
    a.article_id,
    a.title,
    a.description,

    -- username & avatar from student OR admin
    COALESCE(s.username, ad.username) AS username,
    COALESCE(s.avatar_url, ad.avatar_url) AS avatar_url,

    a.slug,
    a.submitted_at,
    c.category AS category_name,

    -- author display name
    COALESCE(s.name, ad.name) AS author_name,
    a.author_type,
    a.content_html

FROM article a

LEFT JOIN category c    
    ON a.category = c.id

LEFT JOIN students s
    ON a.author_id = s.user_id
   AND a.author_type = 'student'

LEFT JOIN admins ad
    ON a.author_id = ad.admin_id
   AND a.author_type IN ('admin', 'teacher')

WHERE a.slug = :slug
  AND a.status = 'published'

LIMIT 1;
";
$stmt = $conn->prepare($sql);
$stmt->execute([':slug' => $slug]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$article){
    die("<h3>Article not found</h3>");
}
?>
<head>
    <title><?php echo htmlspecialchars($article['title']); ?> - Journal</title>
    <meta charset="UTF-8">   
    <meta name="description" content="<?php echo htmlspecialchars($article['description']); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #f5f7fa;
        }
        .article-header {
            background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(25, 118, 210, 0.15);
        }
        .article-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem 0;
            line-height: 1.3;
        }
        .article-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
            font-size: 0.95rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            opacity: 0.95;
        }
        .meta-item a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
            transition: border-color 0.2s;
        }
        .meta-item a:hover {
            border-bottom-color: white;
        }
        .author-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }
        .article-content {
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            line-height: 1.8;
            font-size: 1.05rem;
            color: #333;
        }
        .article-content h2,
        .article-content h3,
        .article-content h4 {
            color: #1976d2;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .article-content p {
            margin-bottom: 1.5rem;
        }
        .article-content img {
            display: block;
            margin: 2rem auto;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .article-content figure {
            text-align: center;
            margin: 2rem 0;
        }
        .article-content figure img {
            max-width: 100%;
            height: auto;
        }
        .article-content figcaption {
            font-size: 0.9rem;
            color: #999;
            margin-top: 0.8rem;
            font-style: italic;
        }
        .article-content blockquote {
            border-left: 4px solid #1976d2;
            padding: 1rem 1.5rem;
            background: #f0f4ff;
            margin: 1.5rem 0;
            border-radius: 4px;
            font-style: italic;
            color: #555;
        }
        .article-content ul,
        .article-content ol {
            margin: 1.5rem 0;
            padding-left: 2rem;
        }
        .article-content li {
            margin-bottom: 0.8rem;
        }
        @media (max-width: 768px) {
            .article-header {
                padding: 1.5rem;
            }
            .article-header h1 {
                font-size: 1.5rem;
            }
            .article-content {
                padding: 1.5rem;
            }
            .article-meta {
                gap: 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<div class="container py-4" style="max-width: 900px;">
    <div class="article-header">
        <div class="author-section">
            <?php if($article['avatar_url']){ ?>
                <img src="<?php echo htmlspecialchars($article['avatar_url']); ?>" alt="Author Avatar" class="author-avatar">
            <?php } ?>
            <div>
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <div class="meta-item">
                        ✍️ <a href="views/profile.php?username=<?php echo urlencode($article['username']); ?>"><?php echo htmlspecialchars($article['author_name']); ?></a>
                    </div>
                    <div class="meta-item">
                        📅 <?php echo date('d M Y', strtotime($article['submitted_at'])); ?>
                    </div>
                    <?php if($article['category_name']){ ?>
                    <div class="meta-item">
                        🏷️ <?php echo htmlspecialchars($article['category_name']); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="article-content">
        <?php echo $article['content_html']; ?>
    </div>
</div>

