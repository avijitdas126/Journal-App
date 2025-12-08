<?php

require_once 'utils/db_conn.php';
require_once __DIR__ . '/utils/base.php';
$method = $_SERVER['REQUEST_METHOD'];
$error = "";   // store error message
switch ($method) {
    case 'POST':
        if (!empty($_POST['role'])) {

            $role = $_POST['role'];
            $name = trim($_POST['name']);
            $username = trim($_POST['username']);
            $college_name = trim($_POST['college_name']);
            $department_id = $_POST['department_id'];
            $password = $_POST['password'];
            if (empty($name) || empty($username) || empty($password)) {
                $error = "All fields are required!";
                break; // show form again
            }
            // Connect DB
            $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

            // Check duplicate username
            $check = $conn->prepare("SELECT username FROM admins WHERE username=?");
            $check->execute([$username]);

            if ($check->rowCount() > 0) {
                $error = "Username already exists!";
                break;
            }
            // Insert admin
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO admins
                (name, username, college_name, department_id, role, password)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $ok = $stmt->execute([
                $name,
                $username,
                $college_name,
                $department_id,
                $role,
                $hash,
            ]);

            if (!$ok) {
                $error = "Failed to create admin. Try again!";
                break;
            }

            header("Location: setup.php?success=1");
            exit;
        } else {
            $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
            db_setup($conn);
            db_close($conn);
            header("Location: http://localhost/Journal/setup.php");
        }
        break;
    case "GET":
        $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
        $sql = "SELECT * FROM `admins` WHERE `role` = 'admin';";
        $success = $conn->query($sql);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <?php
        if (!count($success->fetchAll())) {
            ?>

            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css") ?>" />
                <link rel="stylesheet" href="<?php baseurl("css/style.css") ?>" />
                <title>Add Admin page</title>
            </head>

            <body>
                <div class="container" id="mainfrom">
                    <div class="card" style="width: 30rem" id="formCard">
                        <div class="card-body">
                            <h5 class="card-title">Add User</h5>
                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger">
                                    <?php echo $error; ?>
                                </div>
                            <?php } ?>
                            <form method="post" action="setup.php">
                                <input class="form-control" type="text" placeholder="Enter Name" id="name" value="admin" name="role"
                                    required aria-label="nameHelp" hidden />
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Name:</label>
                                    <input class="form-control" type="text" placeholder="Enter Name" id="name" name="name" required
                                        aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Username:</label>
                                    <input class="form-control" type="text" placeholder="Enter Username" id="username" required
                                        name="username" aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">College Name:</label>
                                    <input class="form-control" type="text" placeholder="Enter College" id="college_name" required
                                        name="college_name" aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Department:</label>
                                    <select class="form-select" aria-label="role" name="department_id" id="department_id" required>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM `departments`;");
                                        $stmt->execute();
                                        $depts = $stmt->fetchAll();
                                        foreach ($depts as $dept) {
                                            ?>
                                            <option value="<?php echo $dept['department_id'] ?>"><?php echo $dept['name'] ?> -
                                                <?php echo $dept['code'] ?>
                                            </option> <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Password:</label>
                                    <input class="form-control" type="password" placeholder="Enter Password " id="student-id"
                                        name="password" aria-label="nameHelp" />
                                </div>

                                <button type="submit" class="btn btn-primary">Add</button>
                            </form>
                        </div>
                    </div>
                </div>

            </body>
            <?php
        } else {
            header("Location: http://localhost/Journal/views/login.php");
        }
        break;

}




function db_setup(PDO &$conn): void
{
    $isExit = table_exists($conn, 'departments');
    if (!$isExit) {
        $sql = "CREATE TABLE `departments` (
        `department_id` INT NOT NULL AUTO_INCREMENT , 
        `name` VARCHAR(50) NOT NULL,
        `code` VARCHAR(50) NOT NULL,
        PRIMARY KEY (`department_id`)
        );";
        $success = $conn->exec(statement: $sql);
        if ($success) {
            die(nl2br(string: "Setup is unsuccessfully"));
        }
    }
    $sql = "INSERT INTO departments (name, code) VALUES
    ('Computer Science', 'CS'),
    ('Mathematics', 'MATH'),
    ('Physics', 'PHYS'),
    ('Chemistry', 'CHEM'),
    ('Zoology', 'ZOO'),
    ('Botany', 'BOT'),
    ('Electronics', 'ELE'),
    ('Economics', 'ECO'),
    ('English', 'ENG'),
    ('Bengali', 'BEN'),
    ('History', 'HIST'),
    ('Political Science', 'POL'),
    ('Philosophy', 'PHIL'),
    ('Sociology', 'SOC'),
    ('Education', 'EDU'),
    ('Geography', 'GEO'),
    ('Commerce', 'COM'),
    ('Accountancy', 'ACC'),
    ('Business Administration', 'BBA'),
    ('Journalism and Mass Communication', 'JMC'),
    ('General', 'GEN'),
    ('Others', 'OTH');";
    $success = $conn->exec(statement: $sql);
    if (!$success) {
        die(nl2br(string: "Setup is unsuccessfully"));
    }
    $isExit = table_exists($conn, 'students');

    if (!$isExit) {
        $sql = "CREATE TABLE `students` (
        `user_id` INT NOT NULL AUTO_INCREMENT , 
        `name` VARCHAR(50) NOT NULL,
        `username` VARCHAR(50) NOT NULL,
        `student_id` VARCHAR(50) DEFAULT NULL , 
        `email`  VARCHAR(50) DEFAULT NULL,
        `college_name` VARCHAR(50) NOT NULL ,
        `university_roll` VARCHAR(50) NOT NULL,
        `department_id` INT,
        `password` VARCHAR(255) NOT NULL,
        `avatar_url` VARCHAR(225) DEFAULT NULL, 
        `createAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        `updateAt` DATETIME NULL DEFAULT NULL ,
        `deleted_At` DATETIME NULL DEFAULT NULL ,
        PRIMARY KEY (`user_id`),
        UNIQUE (`username`),
        FOREIGN KEY (`department_id`) REFERENCES departments(`department_id`)
        );";
        $success = $conn->exec(statement: $sql);
        if ($success) {
            die(nl2br(string: "Setup is unsuccessfully"));

        }
    }
    $isExit = table_exists($conn, 'admins');

    if (!$isExit) {
        $sql = "CREATE TABLE `admins` (
        `admin_id` INT NOT NULL AUTO_INCREMENT , 
        `name` VARCHAR(50) NOT NULL,
        `username` VARCHAR(50) NOT NULL,
        `email`  VARCHAR(50) DEFAULT NULL,
        `college_name` VARCHAR(50) NOT NULL ,
        `department_id` INT,
        `role` enum('teacher', 'developer', 'admin') NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `avatar_url` VARCHAR(225) DEFAULT NULL, 
        `createAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        `updateAt` DATETIME NULL DEFAULT NULL ,
        PRIMARY KEY (`admin_id`),
        UNIQUE (`username`),
        FOREIGN KEY (`department_id`) REFERENCES departments(`department_id`)
        );";
        $success = $conn->exec(statement: $sql);

        if ($success) {
            die(nl2br(string: "Setup is unsuccessfully"));

        }
    }
    $isExit = table_exists($conn, 'category');
    if (!$isExit) {
        $sql = "CREATE TABLE `category` (
        `id` INT NOT NULL AUTO_INCREMENT , 
        `category` VARCHAR(50) NOT NULL,
        `slug` VARCHAR(50) NOT NULL,
        `description` VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
        );";

        $success = $conn->exec(statement: $sql);
        if ($success) {
            die(nl2br(string: "Setup is unsuccessfully"));

        }
        $sql = "INSERT INTO category (category, slug, description) VALUES
        ('Science', 'science', 'Articles related to science'),
        ('Technology', 'technology', 'Articles related to technology'),
        ('Engineering', 'engineering', 'Articles related to engineering'),
        ('Mathematics', 'mathematics', 'Articles related to mathematics'),
        ('Arts', 'arts', 'Articles related to arts'),
        ('Literature', 'literature', 'Articles related to literature'),
        ('History', 'history', 'Articles related to history'),
        ('Philosophy', 'philosophy', 'Articles related to philosophy'),
        ('Social Sciences', 'social-sciences', 'Articles related to social sciences'),
        ('Business', 'business', 'Articles related to business');";
        $success = $conn->exec(statement: $sql);
        if (!$success) {
            die(nl2br(string: "Setup is unsuccessfully"));

        }
        $isExit = table_exists($conn, 'article');
        if (!$isExit) {
            $sql = "CREATE TABLE `article` (`article_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
        `author_id` INT NOT NULL ,
        `author_type`  ENUM('student','admin','teacher') NOT NULL,
        `title` VARCHAR(225) NOT NULL ,
        `despcrition` VARCHAR(225) NULL,
        `category` INT NULL,
        `slug` VARCHAR(225) NOT NULL ,
        `status` ENUM('draft','submitted','approved','rejected','review','published') NOT NULL DEFAULT 'draft' ,
        `content_json` JSON NOT NULL ,
        `content_html` LONGTEXT NOT NULL ,
        `submitted_at` TIMESTAMP NULL ,
        `published_at` TIMESTAMP NULL ,
        `created_at` TIMESTAMP NULL ,
        `updated_at` TIMESTAMP NULL ,
        `deleted_at` TIMESTAMP NULL ,
        PRIMARY KEY (`article_id`) ,
        FOREIGN KEY (`category`) REFERENCES category(`id`)
        );";
            $success = $conn->exec(statement: $sql);
            if ($success) {
                die(nl2br(string: "Setup is unsuccessfully"));

            }
        }
        $isExit = table_exists($conn, 'reviews');
        if (!$isExit) {
            $sql = "CREATE TABLE `reviews` (`review_id` INT NOT NULL AUTO_INCREMENT ,
         `article_id` BIGINT UNSIGNED NOT NULL ,
        `reviewer_id` INT NOT NULL ,
        `reviewer_text` VARCHAR(225) NOT NULL ,
        `created_at` TIMESTAMP NOT NULL ,
        `marks` INT NOT NULL DEFAULT '0' ,
        PRIMARY KEY (`review_id`) ,
        FOREIGN KEY (`article_id`) REFERENCES article(`article_id`),
        FOREIGN KEY (`reviewer_id`) REFERENCES admins(`admin_id`)
        );";
            $success = $conn->exec(statement: $sql);
            if ($success) {
                die(nl2br(string: "Setup is unsuccessfully"));

            }
        }

        $isExit = table_exists($conn, 'asset');
        if (!$isExit) {
            $sql = "CREATE TABLE `asset` (`id` INT NOT NULL AUTO_INCREMENT , `alt` VARCHAR(225) NOT NULL , `url` VARCHAR(225) NOT NULL , `author_id` INT NOT NULL , PRIMARY KEY (`id`) );";
            $success = $conn->exec(statement: $sql);
            if ($success) {
                die(nl2br(string: "Setup is unsuccessfully"));
            }
        }
        $isExit = table_exists($conn, 'revisions');
        if (!$isExit) {
            $sql = "CREATE TABLE revisions (
                    revision_id INT AUTO_INCREMENT PRIMARY KEY,
                    article_id BIGINT UNSIGNED NOT NULL,
                    author_id INT NOT NULL,
                    revision_text TEXT NOT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (article_id) REFERENCES article(article_id)
                    );
                ";
            $success = $conn->exec(statement: $sql);
            if ($success) {
                die(nl2br(string: "Setup is unsuccessfully"));
            }
        }


    }
    echo "Setup successfully";
}




