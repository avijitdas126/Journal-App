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
</head>

<body style="overflow: hidden;">

    <div class="d-flex" style="min-height: 100vh;">
        <div>
            <?php include __DIR__ . '/components/sidebar.php'; ?>
        </div>
        <div style="flex: 1; display: flex; flex-direction: column; overflow-x: hidden;">
            <?php include __DIR__ . '/components/header.php'; ?>
            <main style="flex: 1; overflow-y: auto;">
                <?php
                if ($page == 'article') {
                    include __DIR__ . '/components/article.php';
                } else if ($page == 'add_article'||$page == 'edit_article') {
                    include __DIR__ . '/components/editor.php';
                }
                ?>
            </main>
        </div>


    </div>




</body>


<script src="<?php baseurl("js/bootstrap.bundle.min.js") ?>"></script>


</html>