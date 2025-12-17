<?php
require_once __DIR__ . '/utils/base.php';
// require_once __DIR__ . '/utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
error_reporting(0); // hide warnings

switch ($method) {
    case 'POST':
        if (!file_exists(".env")) {
            $servername = trim($_POST['servername']);
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $db = trim($_POST['db']);
            $content = "servername=$servername\nusername=$username\npassword=$password\ndb=$db\nemail=\npassword_email=";
            $file = fopen('.env', 'w');
            if ($file) {
                fwrite($file, $content);
                fclose($file);
            }
            ?>

            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
                <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
                <title>Installation page</title>
            </head>
            <div class="container">
                <form method="POST" action="setup.php">
                    <button type="submit" class="btn btn-primary">Install Project</button>
                </form>
            </div>

            <?php
        } else {

        }

        break;
    case 'GET':
        if (!file_exists(".env")) {
            ?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
                <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
                <title>Setup page</title>
            </head>

            <body>
                <div class="container" id="mainfrom">
                    <div class="card" style="width: 30rem" id="formCard">
                        <div class="card-body">
                            <h5 class="card-title">Setup Database</h5>
                            <form method="post" action="index.php">
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Server Name:</label>
                                    <input class="form-control" type="text" value="localhost" placeholder="Enter Server name"
                                        id="name" name="servername" required aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Username:</label>
                                    <input class="form-control" type="text" placeholder="Enter Username" id="username" required
                                        name="username" aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Password:</label>
                                    <input class="form-control" type="password" placeholder="Enter Password " id="student-id"
                                        name="password" aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Database:</label>
                                    <input class="form-control" type="text" required placeholder="Enter Database" id="student-id"
                                        name="db" aria-label="nameHelp" />
                                </div>
                                <button type="submit" class="btn btn-primary">Setup</button>
                            </form>
                        </div>
                    </div>
                </div>

            </body>
            <script src="<?php baseurl("js/bootstrap.min.js") ?>"></script>

            </html>
            <?php

        } else {
            header("Location: http://localhost/Journal/main.php");
        }
        break;
    default:
        break;
}
