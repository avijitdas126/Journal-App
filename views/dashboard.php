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



</body>

<script>
    <script src="<?php baseurl("js/bootstrap.min.js") ?>"></script>
</script>

</html>