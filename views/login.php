<?php
require_once __DIR__ . '/../utils/db_conn.php';
error_reporting(0); // hide warnings
$method = $_SERVER['REQUEST_METHOD'];
$error = '';
switch ($method) {
  case 'POST':
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $sql1 = "SELECT * FROM `students` WHERE `username` LIKE '%$username%';";
    $sql2 = "SELECT * FROM `admins` WHERE `username` LIKE '%$username%';";
    $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
    $check1 = $conn->prepare($sql1);
    $check2 = $conn->prepare($sql2);
    $check1->execute();
    $check2->execute();

    if ($check1->rowCount() != 0) {
      $user = $check1->fetchAll()[0];
      $hash = $user['password'];
      $isverify = password_verify($password, $hash);
      if (!$isverify) {
        $error = "This username is not verifyed yet!";
      } else {
        session_start();
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['department_id'] = $user['department_id'];
        $_SESSION['role'] = 'student';
      }
    } else if ($check2->rowCount() != 0) {
      $user = $check2->fetchAll()[0];
      $hash = $user['password'];
      $isverify = password_verify($password, $hash);
      if (!$isverify) {
        $error = "This username is not verifyed yet!";
      } else {
        session_start();
         $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['admin_id'];
        $_SESSION['department_id'] = $user['department_id'];
        $_SESSION['role'] = $user['role'];
      }
    } else {
      $error = "This username is not presented yet!";
    }
    break;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/bootstrap.min.css" />
  <link rel="stylesheet" href="./css/style.css" />
  <title>Login</title>
</head>

<body>
  <div class="container" id="mainfrom">
    <div class="card" style="width: 30rem" id="formCard">
      <div class="card-body">
        <h5 class="card-title" style="margin-bottom: 30px;">Login Page</h5>
        <?php if (!empty($error)) { ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <div><?php echo $error; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php } ?>
        <form method="post" action="login.php">

          <div class="mb-3">
            <label for="exampleInputText1" class="form-label">Username:</label>
            <input class="form-control" type="text" placeholder="Enter Username" id="username" required name="username"
              aria-label="nameHelp" />
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" placeholder="Enter password" name="password" id="password"
              required id="exampleInputPassword1" />
          </div>
          <button type="submit" class="btn btn-primary" id="btn-submit">
            Submit
          </button>
        </form>
      </div>
    </div>
  </div>

</body>
<script src="./js/bootstrap.min.js"></script>

</html>