<?php
require_once __DIR__ . '/../utils/base.php';
session_start();
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

<body>
    <div id="editorjs"></div>
    <script src="<?php baseurl("js/bootstrap.min.js") ?>"></script>
<script src="<?php baseurl("js/lib/editorjs@latest.js") ?>"></script>
    <script src="<?php baseurl("js/lib/header@latest.js") ?>"></script>
    <script src="<?php baseurl("js/lib/list@2.js") ?>"></script>
    <script src="<?php baseurl("js/lib/quote.umd.min.js") ?>"></script>
    <script src="<?php baseurl("js/lib/image@latest.js") ?>"></script>
    <script src="<?php baseurl("js/lib/marker@latest.js") ?>"></script>
    <script src="<?php baseurl("js/lib/underline.umd.min.js") ?>"></script>
    <script src="<?php baseurl("js/lib/raw.js") ?>"></script>
    <script
        src="<?php baseurl("js/lib/checklist@latest.js") ?>"></script>
    <script src="<?php baseurl("js/lib/link@latest.js") ?>"></script>
<script src="<?php baseurl("js/script.js") ?>"></script>
</body>

<script>
    
</script>

</html>