<?php
require_once __DIR__ . '/../utils/db_conn.php';
require_once __DIR__ . '/../utils/base.php';

$conn = db_conn(
    Env('servername'),
    Env('db'),
    Env('username'),
    Env('password')
);
$isOwnProfile = false;
$author_username=null;
session_start();

$author_username = $_GET['username'];


$stmt = $conn->prepare("SELECT * FROM `students` WHERE `username` = ?");
$stmt->execute([$author_username]);
$author = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$author) {
    $stmt = $conn->prepare("SELECT * FROM `admins` WHERE `username` = ?");
    $stmt->execute([$author_username]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$author) {
        // header("Location: 404.php");
        exit;
    } else {
        $role = $author['role'];
        $clg = $author['college_name'];
        $dept_id = $author['department_id'];
        $sql="SELECT * FROM departments WHERE department_id = :id";
        $stm = $conn->prepare($sql);
        $stm->execute([':id' => $author['department_id']]);
        $department = $stm->fetch(PDO::FETCH_ASSOC);
        if($department){
            $dept_id = $department['name'];
        }
        $url = $author['avatar_url'];
    }
}else {
        $role = $author['role'];
        $clg = $author['college_name'];
        $dept_id = $author['department_id'];
        $sql="SELECT * FROM departments WHERE department_id = :id";
        $stm = $conn->prepare($sql);
        $stm->execute([':id' => $author['department_id']]);
        $department = $stm->fetch(PDO::FETCH_ASSOC);
        if($department){
            $dept_id = $department['name'];
        }
        $url = $author['avatar_url'];
}
?>
<style>
    .profile-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
    }
    .profile-header {
        background: linear-gradient(135deg, #4C6EF5, #7950F2);
        padding: 40px;
        text-align: center;
        color: #fff;
    }
    .profile-header img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.8);
        margin-bottom: 15px;
    }
    .profile-body {
        padding: 30px 40px;
    }
    .profile-label {
        font-weight: 600;
        color: #6c757d;
    }
    .profile-value {
        font-size: 1.1rem;
        font-weight: 500;
    }
    body {
    background: whitesmoke;
    font-family: "Open Sans", sans-serif;
}

.container {
    max-width: 960px;
    margin: 30px auto;
    padding: 20px;
}

h1 {
    font-size: 20px;
    text-align: center;
    margin: 20px 0 20px;
    small {
        display: block;
        font-size: 15px;
        padding-top: 8px;
        color: gray;
    }
}

.avatar-upload {
    position: relative;
    max-width: 205px;
    margin: 50px auto;
    .avatar-edit {
        position: absolute;
        right: 12px;
        z-index: 1;
        top: 10px;
        input {
            display: none;
            + label {
                display: inline-block;
                width: 34px;
                height: 34px;
                margin-bottom: 0;
                border-radius: 100%;
                background: #ffffff;
                border: 1px solid transparent;
                box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                cursor: pointer;
                font-weight: normal;
                transition: all 0.2s ease-in-out;
                &:hover {
                    background: #f1f1f1;
                    border-color: #d6d6d6;
                }
                &:after {
                    content: "✏️";
                    font-family: "FontAwesome";
                    color: #757575;
                    position: absolute;
                    top: 10px;
                    left: 0;
                    right: 0;
                    text-align: center;
                    margin: auto;
                }
            }
        }
    }
    .avatar-preview {
        width: 192px;
        height: 192px;
        position: relative;
        border-radius: 100%;
        border: 6px solid #f8f8f8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
        > div {
            width: 100%;
            height: 100%;
            border-radius: 100%;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
    }
}

</style>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?php baseurl("css/bootstrap.min.css"); ?>" />
    <link rel="stylesheet" href="<?php baseurl("css/style.css"); ?>" />
    <title>Profile - <?php echo htmlspecialchars($author['name']); ?></title>
</head>
<div class="container py-5">
    <div class="profile-card">
        <!-- Header -->
        <div class="profile-header">
           
            <img src="<?php echo $url ? $url : "https://dummyimage.com/400x400/000/fff&text=" . urlencode($author['name'][0]); ?>" alt="Avatar">
            <h2 class="mt-3 mb-0"><?php echo htmlspecialchars($author['name']); ?></h2>
            <p class="mb-0" style="font-size: 1rem; opacity: .85;">
                @<?php echo htmlspecialchars($author['username']); ?>
            </p>
        </div>

        <!-- Body -->
        <div class="profile-body">
            <div class="row mb-3">

                <div class="col-md-6 mb-3">
                    <span class="profile-label">College</span>
                    <div class="profile-value">
                        <?php echo htmlspecialchars($clg); ?>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="profile-label">Department</span>
                    <div class="profile-value">
                        <?php echo htmlspecialchars($dept_id); ?>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="profile-label">Email</span>
                    <div class="profile-value">
                        <?php echo htmlspecialchars($author['email'] ?? 'Not provided'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

