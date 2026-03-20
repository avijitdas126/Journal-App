<?php
ini_set('display_errors', 0);
require_once __DIR__ . '/../utils/db_conn.php';
require_once __DIR__ . '/../utils/base.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
switch ($method) {
    case 'POST':
        $otp_array = $_POST['otp'];  // ["8","8","8","8"]
        $username = $_POST['username'];
        $role = $_POST['role'];
        session_start();
        $otp = implode('', $otp_array);  // "8888"
        if (isset($_SESSION['otp'])) {
            $otps = $_SESSION['otp'];
            if ($otp == $otps) {
                if ($role == 'student') {
                    $stmt = $conn->prepare("UPDATE `students` SET `email` = ? WHERE `username` = ?");
                    $ok = $stmt->execute([
                        $_SESSION['email'],
                        $username,
                    ]);
                } else if ($role == 'teacher') {
                    $stmt = $conn->prepare("UPDATE `admins` SET `email` = ? WHERE `username` = ?");
                    $ok = $stmt->execute([
                        $_SESSION['email'],
                        $username,
                    ]);
                }
                unset($_SESSION['otp']);
                header("Location:" . $base_url . "/views/login.php");
            }
        }
        break;
    case 'GET':
        $username = $_GET['username'];
        $role = $_GET['type'];
        break;
}
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon">
    <link rel="icon" href="<?php baseurl("assets/favicon.ico") ?>" type="image/x-icon">
    <title>OTP Verification</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            width: 350px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        h2 {
            margin-bottom: 10px;
        }

        p {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .otp-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .otp-input {
            width: 45px;
            height: 55px;
            font-size: 22px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            transition: 0.2s;
        }

        .otp-input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: #667eea;
            color: #fff;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #5a67d8;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Verify OTP</h2>
        <p>Enter the 4-digit code sent to your email</p>

        <form action="otp.php" method="POST">
            <div class="otp-group">
                <input class="otp-input" type="text" name="otp[]" maxlength="1" required>
                <input class="otp-input" type="text" name="otp[]" maxlength="1" required>
                <input class="otp-input" type="text" name="otp[]" maxlength="1" required>
                <input class="otp-input" type="text" name="otp[]" maxlength="1" required>
                <?php if (isset($_GET['username']) && isset($_GET['type'])) { ?>
                    <input class="otp-input" type="text" name="username" hidden value=<?php echo $username ?> required>
                    <input class="otp-input" type="text" name="role" hidden value=<?php echo $role ?> required>
                <?php } ?>
            </div>

            <button type="submit">Verify</button>
        </form>
    </div>

</body>
<script>
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, i) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
        });
    });
</script>

</html>