<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../utils/db_conn.php';
require_once __DIR__.'/../utils/base.php';
error_reporting(0); // hide warnings
$method = $_SERVER['REQUEST_METHOD'];
$error = '';
switch ($method) {
  case 'POST':
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $sql1 = "SELECT * FROM `students` WHERE `username` = '$username';";
    $sql2 = "SELECT * FROM `admins` WHERE `username` = '$username';";
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
        $_SESSION['avatar_url'] = $user['avatar_url'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = 'student';
      }
      header("Location: dashboard.php?page=overview");
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
        $_SESSION['avatar_url'] = $user['avatar_url'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
      }
      header("Location: dashboard.php?page=overview");
    } else {
      $error = "This username is not presented yet!";
    }
    break;
  case 'GET':
    session_start();
    if (isset($_SESSION['name']) && isset($_SESSION['username']) && isset($_SESSION['user_id']) && isset($_SESSION['department_id']) && isset($_SESSION['role'])) {
      header("Location: dashboard.php?page=overview");
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
  <link rel="shortcut icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon">
  <link rel="icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon"> 
  <link rel="stylesheet" href="./css/style.css" />
  <title>Login - The Digital Scape</title>
  <style>
    body {
      min-height: 100vh;
      margin: 0;
      background:
        radial-gradient(circle at top right, rgba(71, 149, 255, 0.24), transparent 45%),
        radial-gradient(circle at bottom left, rgba(255, 79, 179, 0.20), transparent 45%),
        linear-gradient(120deg, #f4f7ff 0%, #f7f2ff 50%, #f2fbff 100%);
      font-family: "Inter", "Segoe UI", sans-serif;
    }

    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .login-shell {
      width: min(960px, 100%);
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 20px 55px rgba(28, 36, 68, 0.17);
      background: #fff;
    }

    .login-showcase {
      background: linear-gradient(155deg, #0f6dff, #6f2dff 65%, #d2169a);
      color: #fff;
      padding: 2.3rem 2rem;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 1rem;
    }

    .brand-chip {
      width: fit-content;
      padding: 0.3rem 0.75rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.35);
      font-weight: 600;
      font-size: 0.85rem;
      letter-spacing: 0.4px;
    }

    .showcase-title {
      font-size: clamp(1.7rem, 3.8vw, 2.3rem);
      line-height: 1.2;
      font-weight: 700;
      margin: 0;
    }

    .showcase-text {
      margin: 0;
      color: rgba(255, 255, 255, 0.92);
      font-size: 0.95rem;
      line-height: 1.55;
      max-width: 32ch;
    }

    .login-panel {
      padding: 2.2rem 2rem;
      background: #fff;
    }

    .login-title {
      margin: 0;
      color: #202541;
      font-weight: 700;
      font-size: 1.55rem;
    }

    .login-subtitle {
      margin-top: 0.4rem;
      margin-bottom: 1.4rem;
      color: #6a7191;
      font-size: 0.93rem;
    }

    .form-label {
      font-weight: 600;
      color: #3c4260;
      margin-bottom: 0.35rem;
    }

    .form-control {
      height: 47px;
      border-radius: 11px;
      border-color: #dbe3f2;
      padding: 0.65rem 0.9rem;
      font-size: 0.95rem;
    }

    .form-control:focus {
      border-color: #7e65ff;
      box-shadow: 0 0 0 0.2rem rgba(126, 101, 255, 0.18);
    }

    .btn-login {
      width: 100%;
      height: 47px;
      border: 0;
      border-radius: 11px;
      background: linear-gradient(90deg, #145dff, #5a39ff 60%, #d3169b);
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.2px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(77, 57, 222, 0.28);
      color: #fff;
    }

    .login-footnote {
      margin-top: 1rem;
      color: #7a809b;
      font-size: 0.9rem;
      text-align: center;
    }

    .login-footnote a {
      color: #5a39ff;
      text-decoration: none;
      font-weight: 600;
    }

    .login-footnote a:hover {
      text-decoration: underline;
    }

    @media (max-width: 767px) {
      .login-showcase {
        padding: 1.4rem 1.2rem;
      }

      .login-panel {
        padding: 1.5rem 1.2rem;
      }

      .showcase-text {
        max-width: none;
      }
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="login-shell">
      <div class="row g-0">
        <div class="col-md-5">
          <div class="login-showcase">
            <span class="brand-chip">The Digital Scape</span>
            <h1 class="showcase-title">Welcome Back</h1>
            <p class="showcase-text">
              Sign in to continue writing, reviewing, and publishing stories with your college community.
            </p>
          </div>
        </div>

        <div class="col-md-7">
          <div class="login-panel">
            <h2 class="login-title">Login</h2>
            <p class="login-subtitle">Enter your credentials to access your dashboard.</p>

            <?php if (!empty($error)) { ?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <div><?php echo $error; ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php } ?>

            <form method="post" action="login.php">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input class="form-control" type="text" placeholder="Enter username" id="username" required
                  name="username" aria-label="username" />
              </div>

              <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password" id="password"
                  required />
              </div>

              <button type="submit" class="btn btn-login" id="btn-submit">
                Sign In
              </button>
            </form>

            <p class="login-footnote">
              New here? <a href="./signup.php?role=student">Create an account</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
<script src="./js/bootstrap.min.js"></script>

</html>