<?php
require_once __DIR__ . '/../utils/base.php';
session_start();
$title = "Navbar";

if (!isset($_SESSION['name']) && !isset($_SESSION['username']) && !isset($_SESSION['user_id']) && !isset($_SESSION['department_id']) && !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (!isset($_GET['page'])) {
            header("Location: 404.php");
        }
        $page = trim($_GET['page']);
        break;
    default:
        header("Location: 404.php");
        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
    <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
    <title>Dashboard - <?php echo $_SESSION['name'] ?></title>
    <style>
        #main-grid {
            display: grid;
            grid-template-columns: 1fr;
            height: 100vh;
        }
        @media (min-width: 768px) {
            #main-grid {
                grid-template-columns: 280px 1fr;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; overflow: hidden;">

    <div id="main-grid">
        <div>
            <?php include __DIR__ . '/components/sidebar.php'; ?>
        </div>
        <div style="display: flex; flex-direction: column; overflow: hidden;">
            <?php include __DIR__ . '/components/header.php'; ?>
            <main style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <?php
                if ($page == 'article') {
                    include __DIR__ . '/components/article.php';
                } else if ($page == 'add_article'||$page == 'edit_article') {
                    include __DIR__ . '/components/editor.php';
                }else if($page == 'reviews'){
                    include __DIR__ . '/components/review.php';
                }else if($page == 'add_review'){
                    include __DIR__ . '/components/add_review.php';
                }else if($page == 'in_review'){
                    include __DIR__ . '/components/in_review.php';
                }else if($page=='edit_review_article'){
                    include __DIR__ . '/components/student_resubmit.php';
                }else if($page=='add_admin'){
                    include __DIR__ . '/components/add_admin.php';
                }else if($page=='add_category'){
                    include __DIR__ . '/components/add_category.php';
                }
                else if ($page == 'overview') {
                    include __DIR__ . '/components/overview.php';
                }else if($page=='add_developer'){
                    include __DIR__ . '/components/add_developer.php';
                }else if($page=='notice'){
                    include __DIR__ . '/components/notice.php';
                }else if($page=='leaderboard'){
                    include __DIR__ . '/components/leaderboard.php';
                }else {
                    include __DIR__ . '/404.php';
                }
                ?>
            </main>
        </div>
    </div>

</body>

<script src="<?php baseurl("js/bootstrap.bundle.min.js") ?>"></script>

</html>