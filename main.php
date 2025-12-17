<?php
require_once './utils/base.php';
require_once __DIR__ . '/utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        $page = trim($_GET['page'] ?? 'home');
        $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
        $sql1 = "SELECT * FROM notices ORDER BY at_publish DESC LIMIT 5;";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute();
        $notices = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $conn->prepare("SELECT
    u.user_id,
    u.name,
    u.username,
    u.user_type,
    COUNT(DISTINCT a.article_id) AS published_articles,
    COALESCE(SUM(lr.marks),0) AS final_marks,
    (COUNT(DISTINCT a.article_id) * 10 + COALESCE(SUM(lr.marks),0)) AS score
FROM (
    SELECT 
        admin_id AS user_id,
        name,
        username,
        role AS user_type
    FROM admins
    UNION ALL
    SELECT 
        user_id,
        name,
        username,
        'student' AS user_type
    FROM students
) u
LEFT JOIN article a
    ON a.author_id = u.user_id
    AND a.author_type = u.user_type
    AND a.status = 'published'
LEFT JOIN (
    SELECT r1.article_id, r1.marks
    FROM reviews r1
    JOIN (
        SELECT article_id, MAX(created_at) AS last_review_time
        FROM reviews
        GROUP BY article_id
    ) r2
        ON r1.article_id = r2.article_id
        AND r1.created_at = r2.last_review_time
) lr
    ON lr.article_id = a.article_id
GROUP BY 
    u.user_id,
    u.user_type,
    u.name,
    u.username
HAVING published_articles > 0
ORDER BY score DESC;
");
        $stmt->execute();
        $leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sql2 = $sql = "SELECT 
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
ORDER BY a.submitted_at DESC LIMIT 3;
";


        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute();
        $articles = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        break;
    default:
        header("Location: 404.php");
        exit();

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Scriptores </title>
    <link href="<?php baseurl('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding-top: 56px;
            /* Adjust for fixed-top navbar */
        }

        .navbar-brand {
            font-family: 'Merriweather', serif;
            font-size: 1.4rem;
        }

        .hero {
            min-height: 50vh;
            /* Increased min-height for better visual */
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 1rem;
        }

        .hero .container {
            max-width: 900px;
        }

        .hero h1 {
            font-family: 'Merriweather', serif;
            font-size: clamp(2rem, 4.5vw, 3.2rem);
            line-height: 1.2;
            word-wrap: break-word;
        }

        .hero p {
            font-size: clamp(0.95rem, 2.5vw, 1.2rem);
            line-height: 1.5;
        }

        @media (max-width: 576px) {
            .hero {
                min-height: 70vh;
            }

            .hero p {
                display: -webkit-box;
                -webkit-line-clamp: 4;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        }

        .badge-verify {
            background: #ffc107;
            color: #000;
        }

        .article-card {
            transition: transform .3s, box-shadow .3s;
            animation: fadeIn 0.8s ease-out;
            /* Add animation for pagination effect */
        }

        .article-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, .15);
        }

        .leaderboard li {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }

        /* Category List Styles */
        .category-list {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 10px;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        .category-list::-webkit-scrollbar {
            height: 6px;
        }

        .category-list::-webkit-scrollbar-thumb {
            background: #ced4da;
            border-radius: 3px;
        }

        .category-item {
            display: inline-block;
            margin-right: 10px;
            transition: transform .2s;
        }

        .category-item:hover {
            transform: translateY(-2px);
        }

        /* Notice Marquee Styles */
        .notice-marquee {
            display: flex;
            background-color: #20c997;
            /* A vibrant color for attraction */
            color: white;
            padding: 10px 0;
            overflow: hidden;
            white-space: nowrap;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        .notice-marquee strong {
            font-weight: 700;
            margin-right: 15px;
        }

        /* Animation for article cards */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer {
            background: var(--bs-primary);
            color: #fff;
        }

        .notice-board-anim {
            height: 110px;
            /* Adjust to fit one notice at a time */
            overflow: hidden;
            position: relative;
        }

        .notice-list {
            display: flex;
            flex-direction: column;
            animation: noticeScroll 6s linear infinite;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .notice-list li {
            min-height: 110px;
            margin: 0;
            padding: 0;
        }

        .notice-list li {
            min-height: 110px;
            margin: 0;
            padding: 0;
            transition: box-shadow 0.3s, background 0.3s;
        }

        .active-notice {
            box-shadow: 0 4px 16px rgba(32, 201, 151, 0.15);
            background: #e0f7ef !important;
            border-left: 4px solid #20c997;
        }

        footer {
            background: var(--bs-primary);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 40px 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr;
            gap: 60px;
            position: relative;
            z-index: 1;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .footer-column h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            width: fit-content;
        }

        .arrow-icon {
            display: inline-block;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 8px solid white;
        }

        .footer-links a:hover {
            color: #ffff;
            font-weight: 600;
            padding-left: 5px;
        }

        .footer-bottom {
            background: rgba(201, 169, 97, 0.9);
            padding: 20px 40px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .back-to-top {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            width: fit-content;
        }

        .back-to-top:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

        .footer-bottom p {
            font-size: 14px;
            color: #000;
            font-weight: 500;
        }

        @media (max-width: 968px) {
            .footer-content {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
                padding: 50px 30px 30px;
            }

            .footer-brand {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 40px 20px 30px;
            }

            .logo-text {
                font-size: 24px;
            }

            .footer-description {
                font-size: 14px;
            }

            .footer-column h3 {
                font-size: 16px;
            }

            .footer-bottom {

                padding: 15px 20px;
            }

            .footer-bottom p {
                font-size: 12px;
            }
        }

        @keyframes noticeScroll {
            0% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-110px);
            }

            90% {
                transform: translateY(-110px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .notice-board-anim .notice-list li {
            transition: box-shadow 0.2s, transform 0.2s, background 0.2s;
            cursor: pointer;
        }

        .notice-board-anim .notice-list li:hover,
        .notice-board-anim .notice-list li:focus {
            background: #eaf6f4;
            box-shadow: 0 4px 18px rgba(32, 201, 151, 0.10);
            transform: scale(1.02);
            z-index: 2;
            position: relative;
        }

        .notice-board-anim .notice-list li.active,
        .notice-board-anim .notice-list li:active {
            background: #d1f2eb;
            box-shadow: 0 6px 24px rgba(32, 201, 151, 0.18);
            transform: scale(1.04);
            z-index: 3;
            position: relative;
        }

        .logo-text {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="#">Scriptores</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link <?php if ($page == 'home') {
                        echo 'active';
                    } else {
                        echo '';
                    } ?>" href="?page=home">Home</a></li>
                    <li class="nav-item"><a class="nav-link <?php if ($page == 'articles') {
                        echo 'active';
                    } else {
                        echo '';
                    } ?>" href="?page=articles">Articles</a></li>
                    <li class="nav-item"><a class="nav-link <?php if ($page == 'leaderboard') {
                        echo 'active';
                    } else {
                        echo '';
                    } ?>" href="?page=leaderboard">Leaderboard</a></li>
                    <li class="nav-item"><a class="nav-link <?php if ($page == 'notices') {
                        echo 'active';
                    } else {
                        echo '';
                    } ?>" href="?page=notices">Notices</a></li>
                    <li class="nav-item d-flex align-items-center ms-lg-2">
                        <a class="btn  btn-outline-light me-2" href="views/login.php">Login</a>
                        <a class="btn btn-warning" href="views/signup.php?role=student">Join with Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($page == 'home') { ?>

        <?php if (count($notices) > 0) { ?>
            <div class="notice-marquee">
                <marquee behavior="scroll" direction="left" scrollamount="8" onmouseover="this.stop();"
                    onmouseout="this.start();">
                    <?php foreach ($notices as $notice) { ?>
                        <a href=<?php echo htmlspecialchars($notice['url']); ?> target="_blank"
                            style="color:white; text-decoration:none;"><strong>📢
                                <?php echo htmlspecialchars($notice['title']); ?>:</strong> &bull;</a>
                    <?php } ?>

                </marquee>
            </div>
        <?php } ?>
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="5000">
                    <section class="hero text-center"
                        style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url('<?php baseurl('assets/header.jfif') ?>');background-size:cover;background-position:center;">
                        <div class="container">
                            <h1>Connecting Readers & Student Journalists</h1>
                            <p class="lead mt-3">
                                Our mission is to build a meaningful relationship between readers and student writers,
                                encouraging ethical journalism, informed voices, and campus dialogue.
                            </p>
                            <span class="badge badge-verify mt-3">🏆 Leaderboard celebrates top student journalists</span>
                        </div>
                    </section>
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                    <section class="hero text-center"
                        style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url('<?php baseurl('assets/header-1.jfif') ?>');background-size:cover;background-position:center;">
                        <div class="container">
                            <h1>Student Voices, Campus Stories</h1>
                            <p class="lead mt-3">
                                Showcasing authentic campus reporting written by students and read by the community.
                            </p>

                        </div>
                    </section>
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                    <section class="hero text-center"
                        style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url('<?php baseurl('assets/header-2.jfif') ?>');background-size:cover;background-position:center;">
                        <div class="container">
                            <h1>Read. Write. Lead.</h1>
                            <p class="lead mt-3">
                                Empowering the next generation of journalists through ethical reporting and leadership.
                            </p>
                            <span class="badge badge-verify mt-3">🎓 Department of Journalism Initiative</span>
                        </div>
                    </section>
                </div>

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <section class="container my-5">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <strong class="fs-5">📄 NOTICE Board</strong>
                </div>
                <div class="card-body" style="background:#eaf6f4">
                    <div class="notice-board-anim">
                        <ul class="notice-list">
                            <?php foreach ($notices as $index => $notice) { ?>
                                <li>
                                    <a href="<?php echo htmlspecialchars($notice['url']); ?>" target="_blank"
                                        style="color:black; text-decoration:none;">
                                        <div class="d-flex align-items-center bg-white rounded p-3 mb-3 shadow-sm">
                                            <div class="me-3 fs-2">📋</div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold"><?php echo htmlspecialchars($notice['title']); ?></div>
                                                <span class="badge bg-success mt-1">NEW</span>
                                            </div>
                                            <div class="text-center ms-3">
                                                <div class="fs-4 fw-bold">
                                                    <?php echo htmlspecialchars(date('d', strtotime($notice['at_publish']))); ?>
                                                </div>
                                                <small
                                                    class="text-muted"><?php echo htmlspecialchars(date('M', strtotime($notice['at_publish']))); ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <a href="?page=notices" class="btn btn-success w-100 mt-3">VIEW ALL</a>
                </div>
            </div>
        </section>


        <section class="container my-5">
            <div class="d-flex" style="align-items: center;justify-content: space-between;">
                <h2 class="mb-4">Latest Approved Articles</h2>
                <a href="?page=articles"><button type="button" class="btn btn-success">View All Articles</button></a>
            </div>

            <div id="articles-page-1" class="row g-4 article-page">
                <?php foreach ($articles as $article) { ?>
                    <a href="?page=article&slug=<?php echo $article['slug']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="col-md-4"></div>

                        <div>
                            <div class="card article-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                    <p class="text-muted small">✍️ <?php echo htmlspecialchars($article['author_name']); ?> · 📅
                                        <?php echo date('d M', strtotime($article['submitted_at'])); ?>
                                    </p>
                                    <p class="card-text mt-2"><?php echo htmlspecialchars($article['description']); ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php } ?>

            </div>
        </section>

        <section class="container my-5">
            <h2 class="mb-4">🏆 Top Authors Leaderboard</h2>
            <ul class="list-unstyled leaderboard">
                <?php
                $rank = 1;
                foreach ($leaders as $leader) { ?>
                    <li>
                        <span><?php echo $rank++; ?>. <?php echo htmlspecialchars($leader['name']); ?></span>
                        <span><?php echo htmlspecialchars($leader['final_marks']); ?> Points</span>
                    </li>
                <?php } ?>
            </ul>
        </section>

        <section class="container my-5">
            <h2 class="mb-3">Faculty Review Process</h2>
            <p>
                Articles are submitted by students and reviewed by journalism faculty members.
                Only approved articles are published to maintain ethical and academic standards.
            </p>
        </section>
    <?php } else if ($page == 'leaderboard') {
        include_once './views/components/leaderboard.php';
    } else if ($page == 'articles') {
        include_once './views/components/articles.php';
    } else if ($page == 'notices') {
        include_once './views/components/notices.php';
    } else if ($page == 'article') {
        include_once './views/components/article_detail.php';
    } else if ($page == 'terms_of_services') {
        include_once './views/components/terms_of_services.php';
    } else if ($page == 'privacy_policy') {
        include_once './views/components/privacy_policy.php';
    } else if ($page == 'contact') {
        include_once './views/components/contact.php';
    } else {
        include_once './views/404.php';
    } ?>
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo-container">
                    <div class="logo-icon"></div>
                    <span class="logo-text">Scriptores</span>
                </div>
                <p class="footer-description">
                    A single Platform for all students of behala college to write and share their ideas and thoughts.
                </p>
                <button class="back-to-top" id="backToTop">
                    <span class="arrow-icon"></span>
                    BACK TO TOP
                </button>
            </div>

            <div class="footer-column">
                <h3>Essentials</h3>
                <div class="footer-links">
                    <a href="?page=articles">Resources & news</a>
                    <a href="?page=notices">Notice Board</a>
                    <a href="?page=contact">Contact Us</a>
                    <a href="?page=library">Library</a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Legal</h3>
                <div class="footer-links">
                    <a href="?page=privacy_policy">Privacy Policy</a>
                    <a href="?page=terms_of_services">Terms of Services</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>Copyright © 2024, Scriptores, All Rights Reserved.</p>
        </div>
    </footer>

    <script src="<?php baseurl('js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        const backToTopBtn = document.getElementById('backToTop');

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        const notices = document.querySelectorAll('.notice-list li');
        let current = 0;
        document.addEventListener('DOMContentLoaded', function () {
            const pages = document.querySelectorAll('.article-page');
            const paginationLinks = document.querySelectorAll('.pagination .page-item');
            const prevButton = document.getElementById('prev-page');
            const nextButton = document.getElementById('next-page');
            let currentPage = 1;

            function showPage(pageNumber) {
                pages.forEach(page => {
                    page.style.display = 'none';
                });
                document.getElementById(`articles-page-${pageNumber}`).style.display = 'flex'; // Use flex for row

                // Re-trigger fade-in animation for cards on the new page
                document.querySelectorAll(`#articles-page-${pageNumber} .article-card`).forEach(card => {
                    card.style.animation = 'none';
                    card.offsetHeight; /* Trigger reflow */
                    card.style.animation = '';
                });

                paginationLinks.forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`.pagination .page-item[data-page="${pageNumber}"]`).classList.add('active');

                // Update disabled states
                prevButton.classList.toggle('disabled', pageNumber === 1);
                nextButton.classList.toggle('disabled', pageNumber === pages.length);

                currentPage = pageNumber;
            }

            paginationLinks.forEach(item => {
                const pageNum = parseInt(item.getAttribute('data-page'));
                if (pageNum) {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        showPage(pageNum);
                    });
                }
            });

            prevButton.addEventListener('click', function (e) {
                if (currentPage > 1) {
                    e.preventDefault();
                    showPage(currentPage - 1);
                }
            });

            nextButton.addEventListener('click', function (e) {
                if (currentPage < pages.length) {
                    e.preventDefault();
                    showPage(currentPage + 1);
                }
            });

            // Initialize with the first page
            showPage(1);
        });
    </script>
</body>

</html>