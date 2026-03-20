<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../utils/db_conn.php';
require_once __DIR__ . '/../utils/base.php';
require __DIR__ . '/../vendor/autoload.php';
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

$validator = new EmailValidator();
$multipleValidations = new MultipleValidationWithAnd([
  new RFCValidation(),
  new DNSCheckValidation()
]);
// var_dump($b);
error_reporting(1); // hide warnings
$conn = db_conn(
  Env('servername'),
  Env('db'),
  Env('username'),
  Env('password')
);
$type = "";
$error = "";
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
  case 'POST':
    $role = $_POST['role'] ?? '';
    $name = $_POST['name'];
    $username = $_POST['username'];
    $student_id = $_POST['student-id'];
    $university_roll = $_POST['university_roll'];
    $college_name = $_POST['college-name'];
    $department_id = $_POST['department_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (empty($name) || empty($username) || empty($password)) {
      $error = "All fields are required!";
      break; // show form again
    }
    if (!$validator->isValid($email, $multipleValidations)) {
      $error = "Enter an correct email address.";
      break; // show form again
    }
    if (strlen($password) < 6) {
      $error = "Minimun length of password is 6.";
      break; // show form again
    }

    // Connect DB
    $conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));


    $hash = password_hash($password, PASSWORD_BCRYPT);
    // Check duplicate username
    $check = $conn->prepare("SELECT username FROM admins WHERE username=?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
      $error = "Username already exists!";
      break;
    }
    $check = $conn->prepare("SELECT username FROM students WHERE username=?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
      $error = "Username already exists!";
      break;
    }
    if ($role == 'teacher') {
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
        $error = "Failed to create teacher. Try again!";
        break;
      } else {
        session_start();
        $type = "otp";
        $otp = random_int(1000, 9999);
        $_SESSION['otp']=$otp;
        $_SESSION['email']=$email;
        $_SESSION['name']=$name;
        include __DIR__ . "/../mail.php";
        $body = '
              <!DOCTYPE html>
              <html lang="en">
              <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>OTP Verification</title>
              <style>
                body {
                  margin: 0;
                  padding: 0;
                  background-color: #f4f4f7;
                  font-family: Arial, sans-serif;
                }
                .container {
                  max-width: 500px;
                  margin: 40px auto;
                  background: #ffffff;
                  padding: 30px;
                  border-radius: 8px;
                  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                  text-align: center;
                }
                .logo {
                  font-size: 22px;
                  font-weight: bold;
                  margin-bottom: 20px;
                }
                .otp {
                  font-size: 32px;
                  letter-spacing: 6px;
                  font-weight: bold;
                  color: #333;
                  margin: 20px 0;
                }
                .message {
                  font-size: 14px;
                  color: #555;
                  margin-bottom: 20px;
                }
                .footer {
                  font-size: 12px;
                  color: #999;
                  margin-top: 30px;
                }
              </style>
              </head>
              <body>

              <div class="container">
                <div class="logo">The Digital Scape</div>

                <p class="message">
                  Use the OTP below to complete your verification. This code is valid for 5 minutes.
                </p>

                <div class="otp">' . $otp . '</div>


                <div class="footer">
                  © 2026 The Digital Scape. All rights reserved.
                </div>
              </div>

              </body>';
        $altbody = "Your OTP is: $otp. It is valid for 5 minutes. Do not share this code.";
        $subject = "Your OTP Code - Verification Required";
        sendMailToNewAdmin($email, $name, $subject, $body, $altbody);

        header("Location:" . $base_url . "/views/otp.php?username=". $username ."&type=".$role);
      }
    } else {
      // Check duplicate username
      $stmt = $conn->prepare("INSERT INTO students
                (name, username,student_id,university_roll,college_name, department_id, password)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
      );

      $ok = $stmt->execute([
        $name,
        $username,
        $student_id,
        $university_roll,
        $college_name,
        $department_id,
        $hash,
      ]);
      if (!$ok) {
        $error = "Failed to create student. Try again!";
        break;
      } else {
        session_start();
        $type = "otp";
        $otp = random_int(1000, 9999);
        $_SESSION['otp']=$otp;
        $_SESSION['email']=$email;
        $_SESSION['name']=$name;
        include __DIR__ . "/../mail.php";
        $body = '
              <!DOCTYPE html>
              <html lang="en">
              <head>
              <meta charset="UTF-8">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>OTP Verification</title>
              <style>
                body {
                  margin: 0;
                  padding: 0;
                  background-color: #f4f4f7;
                  font-family: Arial, sans-serif;
                }
                .container {
                  max-width: 500px;
                  margin: 40px auto;
                  background: #ffffff;
                  padding: 30px;
                  border-radius: 8px;
                  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                  text-align: center;
                }
                .logo {
                  font-size: 22px;
                  font-weight: bold;
                  margin-bottom: 20px;
                }
                .otp {
                  font-size: 32px;
                  letter-spacing: 6px;
                  font-weight: bold;
                  color: #333;
                  margin: 20px 0;
                }
                .message {
                  font-size: 14px;
                  color: #555;
                  margin-bottom: 20px;
                }
                .footer {
                  font-size: 12px;
                  color: #999;
                  margin-top: 30px;
                }
              </style>
              </head>
              <body>

              <div class="container">
                <div class="logo">The Digital Scape</div>

                <p class="message">
                  Use the OTP below to complete your verification. This code is valid for 5 minutes.
                </p>

                <div class="otp">' . $otp . '</div>


                <div class="footer">
                  © 2026 The Digital Scape. All rights reserved.
                </div>
              </div>

              </body>';
        $altbody = "Your OTP is: $otp. It is valid for 5 minutes. Do not share this code.";
        $subject = "Your OTP Code - Verification Required";
        sendMailToNewAdmin($email, $name, $subject, $body, $altbody);

        header("Location:" . $base_url . "/views/otp.php?username=". $username ."&type=".$role);
      }
    }

    break;
  case 'GET':
    $role = $_GET['role'] ?? '';
    if (empty($role) || ($role != 'teacher' && $role != 'student')) {
      header("Location: 404.php");
      exit;
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
  <title>Create a new account - The Digital Scape</title>
  <style>
    .spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #ddd;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  margin: 0 auto;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
    body {
      min-height: 100vh;
      margin: 0;
      background:
        radial-gradient(circle at top right, rgba(71, 149, 255, 0.24), transparent 45%),
        radial-gradient(circle at bottom left, rgba(255, 79, 179, 0.20), transparent 45%),
        linear-gradient(120deg, #f4f7ff 0%, #f7f2ff 50%, #f2fbff 100%);
      font-family: "Inter", "Segoe UI", sans-serif;
    }

    .signup-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .signup-shell {
      width: min(1080px, 100%);
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 20px 55px rgba(28, 36, 68, 0.17);
      background: #fff;
    }

    .signup-showcase {
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
      max-width: 34ch;
    }

    .signup-panel {
      padding: 2.2rem 2rem;
      background: #fff;
    }

    .nav-pills .nav-link {
      font-weight: 600;
      font-size: 0.93rem;
      border-radius: 999px;
      margin-right: 0.45rem;
      padding: 0.45rem 0.9rem;
      border: 1px solid #dbe3f2;
      color: #3f4868;
      background: #fff;
      transition: all 0.2s ease;
    }

    .nav-pills .nav-link.active {
      background: linear-gradient(90deg, #145dff, #5a39ff 60%, #d3169b);
      color: #fff;
      border-color: transparent;
      box-shadow: 0 8px 20px rgba(77, 57, 222, 0.25);
    }

    .signup-title {
      margin: 1.15rem 0 0;
      color: #202541;
      font-weight: 700;
      font-size: 1.45rem;
    }

    .signup-subtitle {
      margin: 0.35rem 0 1.2rem;
      color: #6a7191;
      font-size: 0.93rem;
    }

    .form-label {
      font-weight: 600;
      color: #3c4260;
      margin-bottom: 0.35rem;
    }

    .form-control,
    .form-select {
      height: 47px;
      border-radius: 11px;
      border-color: #dbe3f2;
      padding: 0.65rem 0.9rem;
      font-size: 0.95rem;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #7e65ff;
      box-shadow: 0 0 0 0.2rem rgba(126, 101, 255, 0.18);
    }

    .btn-signup {
      width: 100%;
      height: 47px;
      border: none;
      border-radius: 11px;
      background: linear-gradient(90deg, #145dff, #5a39ff 60%, #d3169b);
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.2px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-signup:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(77, 57, 222, 0.28);
      color: #fff;
    }

    .alert {
      border-radius: 10px;
      margin-bottom: 1rem;
    }

    .signup-footnote {
      margin-top: 1rem;
      color: #7a809b;
      font-size: 0.9rem;
      text-align: center;
    }

    .signup-footnote a {
      color: #5a39ff;
      text-decoration: none;
      font-weight: 600;
    }

    .signup-footnote a:hover {
      text-decoration: underline;
    }

    @media (max-width: 767px) {
      .signup-showcase {
        padding: 1.4rem 1.2rem;
      }

      .signup-panel {
        padding: 1.5rem 1.2rem;
      }

      .showcase-text {
        max-width: none;
      }
    }
  </style>
</head>

<body>
  <div class="signup-wrapper">
    <div class="signup-shell">
      <?php if (empty($type)) { ?>
        <div class="row g-0">
          <div class="col-md-5">
            <div class="signup-showcase">
              <span class="brand-chip">The Digital Scape</span>
              <h1 class="showcase-title">Create Account</h1>
              <p class="showcase-text">
                Join the platform and start sharing your ideas, articles, and campus stories with your community.
              </p>
            </div>
          </div>

          <div class="col-md-7">
            <div class="signup-panel">
              <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                  <a class="nav-link <?php if ($role == 'student') { ?> active disabled <?php } ?>" aria-current="page"
                    href="signup.php?role=student">Student</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link <?php if ($role == 'teacher') { ?> active disabled <?php } ?>"
                    href="signup.php?role=teacher">Teacher</a>
                </li>
              </ul>

              <h2 class="signup-title">Join as
                <?php if ($role == 'student') { ?>Student<?php } else if ($role == 'teacher') { ?>Teacher<?php } ?>
              </h2>
              <p class="signup-subtitle">Fill in your details to create your account.</p>

              <?php if (!empty($error)) { ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                  <div><?php echo $error; ?></div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php } ?>

              <form method="post" action="signup.php">
                <input type="text" hidden value="<?php echo $role; ?>" name="role">

                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input class="form-control" type="text" placeholder="Enter name" id="name" name="name" required
                    aria-label="name" />
                </div>

                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input class="form-control" type="text" placeholder="Enter username" id="username" required
                    name="username" aria-label="username" />
                </div>

                <?php
                if ($role == 'student') {
                  ?>
                  <div class="mb-3">
                    <label for="student-id" class="form-label">Student ID</label>
                    <input class="form-control" type="text" placeholder="Enter student ID" id="student-id" name="student-id"
                      aria-label="student-id" />
                  </div>

                  <div class="mb-3">
                    <label for="university_roll" class="form-label">University Roll</label>
                    <input class="form-control" type="text" placeholder="Enter university roll" id="university_roll"
                      name="university_roll" aria-label="university-roll" />
                  </div>
                  <?php
                }
                ?>

                <div class="mb-3">
                  <label for="college" class="form-label">College Name</label>
                  <input class="form-control" type="text" name="college-name" id="college" required
                    placeholder="Enter college name" aria-label="college-name" />
                </div>

                <div class="mb-3">
                  <label for="department_id" class="form-label">Department</label>
                  <select class="form-select" aria-label="department" name="department_id" id="department_id" required>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM `departments`;");
                    $stmt->execute();
                    $depts = $stmt->fetchAll();
                    foreach ($depts as $dept) {
                      ?>
                      <option value="<?php echo $dept['department_id'] ?>"><?php echo $dept['name'] ?> -
                        <?php echo $dept['code'] ?>
                      </option>
                      <?php
                    }
                    ?>
                  </select>
                </div>

                <div class="mb-4">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required />
                </div>
                <div class="mb-4">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" name="password" id="password" placeholder="Enter password"
                    required />
                </div>

                <button type="submit" class="btn btn-signup" id="btn-submit">
                  Create Account
                </button>
              </form>

              <p class="signup-footnote">
                Already have an account? <a href="./login.php">Login</a>
              </p>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>


</body>
<script src="./js/bootstrap.min.js"></script>

</html>