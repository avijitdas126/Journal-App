<?php
require_once __DIR__ . '/../utils/db_conn.php';
error_reporting(0); // hide warnings
$conn = db_conn(
  Env('servername'),
  Env('db'),
  Env('username'),
  Env('password')
);
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
    $password = $_POST['password'];
    if (empty($name) || empty($username) || empty($password)) {
      $error = "All fields are required!";
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
        header("Location: http://localhost/journal/views/login.php");
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
        header("Location: http://localhost/journal/views/login.php");
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
  <link rel="stylesheet" href="./css/style.css" />
  <title>SignUp</title>
  <style>
    body {
      background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    #mainfrom {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 0;
    }

    #formCard {
      box-shadow: 0 8px 32px rgba(25, 118, 210, 0.10);
      border-radius: 18px;
      border: none;
      animation: fadeInUp 0.7s cubic-bezier(.39, .575, .56, 1.000);
      background: #fff;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .nav-pills .nav-link {
      font-weight: 500;
      font-size: 1.05rem;
      border-radius: 8px;
      margin-right: 8px;
      transition: background 0.2s, color 0.2s;
    }

    .nav-pills .nav-link.active {
      background: #1976d2;
      color: #fff;
      box-shadow: 0 2px 8px rgba(25, 118, 210, 0.10);
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 600;
      color: #1976d2;
      margin-bottom: 18px !important;
    }

    .form-label {
      font-weight: 500;
      color: #1976d2;
    }

    .form-control,
    .form-select {
      border-radius: 8px;
      border: 1.5px solid #e3e3e3;
      font-size: 1rem;
      margin-bottom: 8px;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #1976d2;
      box-shadow: 0 0 0 2px rgba(25, 118, 210, 0.10);
    }

    .btn-primary {
      background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1.1rem;
      width: 100%;
      padding: 10px 0;
      margin-top: 10px;
      box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08);
      transition: background 0.2s, transform 0.2s;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #1565c0 0%, #1976d2 100%);
      transform: translateY(-2px) scale(1.02);
    }

    .alert {
      border-radius: 8px;
      font-size: 1rem;
      margin-bottom: 18px;
    }
  </style>
</head>

<body>
  <div class="container" id="mainfrom">
    <div class="card" style="width: 30rem;padding:20px;" id="formCard">
      <div style="margin:auto;">
      <ul class="nav nav-pills">
        <li class="nav-item">

          <a class="nav-link <?php if ($role == 'student') { ?> active disabled <?php } ?>" aria-current="page"
            href="signup.php?role=student">Join Us as Student</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php if ($role == 'teacher') { ?> active disabled <?php } ?>"
            href="signup.php?role=teacher">Join Us as Teacher</a>
        </li>

      </ul>
      </div>
      <div class="card-body" style="margin-top: 15px;">
        <h5 class="card-title" style="margin-bottom:15px;">Join With Us as <?php if ($role == 'student') { ?> Student
          <?php } else if ($role == 'teacher') { ?>Teacher <?php } ?>
        </h5>
        <?php if (!empty($error)) { ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <div><?php echo $error; ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php } ?>
        <form method="post" action="signup.php">
          <input type="text" hidden value="<?php echo $role; ?>" name="role">
          <div class="mb-3">
            <label for="exampleInputText1" class="form-label">Name:</label>
            <input class="form-control" type="text" placeholder="Enter Name" id="name" name="name" required
              aria-label="nameHelp" />
          </div>
          <div class="mb-3">
            <label for="exampleInputText1" class="form-label">Username:</label>
            <input class="form-control" type="text" placeholder="Enter Username" id="username" required name="username"
              aria-label="nameHelp" />
          </div>
          <?php
          if ($role == 'student') {
            ?>
            <div class="mb-3">
              <label for="exampleInputText1" class="form-label">Student id:</label>
              <input class="form-control" type="text" placeholder="Enter Student id " id="student-id" name="student-id"
                aria-label="nameHelp" />
            </div>
            <div class="mb-3">
              <label for="exampleInputText1" class="form-label">University Roll:</label>
              <input class="form-control" type="text" placeholder="Enter University Roll" id="university_roll"
                name="university_roll" aria-label="nameHelp" />
            </div>
            <?php
          }


          ?>
          <div class="mb-3">
            <label for="exampleInputText1" class="form-label">College Name:</label>
            <input class="form-control" type="text" name="college-name" id="college" required
              placeholder="Enter College Name" aria-label="CollegeHelp" />
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
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password"
              required id="exampleInputPassword1" />
          </div>
          <div>
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