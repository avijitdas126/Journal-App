<?php
require_once __DIR__ . '/../../utils/db_conn.php';
require_once __DIR__ . '/../../utils/base.php';
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
  case 'POST':
    // print_r($_POST);
    session_start();
    
    $conn = db_conn(
        Env('servername'),
        Env('db'),
        Env('username'),
        Env('password')
    );
    $fileUrl = $_POST['url'] ?? '';
    if (!empty($_POST['cropped_image'])) {
    
    $uploadDir = __DIR__ . "/../../uploads/";
    $data = $_POST['cropped_image'];

    // Extract base64 data
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);

    $data = base64_decode($data);

    $fileName = uniqid() . '.png';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    file_put_contents($uploadDir . $fileName, $data);

    $fileUrl = $base_url . "/uploads/" . $fileName;

    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $userId=trim($_POST['user_id']);
    $role=trim($_POST['role']);
    if($role == 'student') {
    $sql = "UPDATE `students` SET `name` = :name, `email` = :email, `avatar_url` = :avatar_url WHERE `user_id` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':avatar_url' => $fileUrl,
        ':id' => $userId
    ]);
    } else {
        $sql = "UPDATE `admins` SET `name` = :name, `email` = :email, `avatar_url` = :avatar_url WHERE `admin_id` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':avatar_url' => $fileUrl,
            ':id' => $userId
        ]);
    }
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    if ($fileUrl) {
        $_SESSION['avatar_url'] = $fileUrl;
    }
    header("Location: $base_url/views/dashboard.php?page=update_profile&status=success");
    break;
  case 'GET':
    session_start();
    $name = $_SESSION['name'] ?? '';
    $email = $_SESSION['email'] ?? '';
    $userId = $_SESSION['user_id'];
    break;
}
?>
<head>
    <link rel="stylesheet" href="https://fengyuanchen.github.io/cropperjs/css/cropper.css">
    <style>
        .cropper-view-box,
    .cropper-face {
      border-radius: 50%;
    }

    /* The css styles for `outline` do not follow `border-radius` on iOS/Safari (#979). */
    .cropper-view-box {
        outline: 0;
        box-shadow: 0 0 0 1px #39f;
    }
    </style>
</head>
<div class="container p-3">
    <h2>Update Profile</h2>
    <p>This is the update profile page. You can update your profile information here.</p>
    <!-- <div class="card" style="width: 30rem;"> -->
    <!-- <div class="card-body"> -->
    <form id="profileForm" method="POST" action="components/update_profile.php" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="file-upload" class="form-label">Profile Picture:</label>
        <input type="file" class="form-control" id="file-upload" accept="image/*">
    </div>
    <input type="text" name="user_id" value="<?php echo $userId; ?>" hidden>
    <input type="text" name="role" value="<?php echo $_SESSION['role'] ?? ''; ?>" hidden>
    <div  class=" d-flex flex-column gap-2" >
        <div style="max-width: 400px;">
            <img id="file-preview" src="" alt="Preview" style="display:none; max-width:100%;">
        </div>
        <button type="button" style="max-width: 100px;display:none;" class="btn btn-primary" id="button">Crop</button>
    </div>
    <input type="hidden" hidden name="cropped_image" id="cropped_image">

    <input type="hidden" hidden name="url" value="<?php echo $_SESSION['avatar_url'] ?? ''; ?>" id="url">
<!-- <img src="#" alt="" hidden name="cropped_image" id="cropped_image"> -->
 
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $_SESSION['name'] ?? ''; ?>" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?php echo $_SESSION['email'] ?? ''; ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>
    <!-- </div> -->
    <!-- </div> -->
</div>
<script src="https://fengyuanchen.github.io/cropperjs/js/cropper.js"></script>
<script>
const input = document.getElementById('file-upload');
const preview = document.getElementById('file-preview');
const form = document.getElementById('profileForm');
const croppedInput = document.getElementById('cropped_image');
const cropButton = document.getElementById('button');
var cropper;
   function getRoundedCanvas(sourceCanvas) {
      var canvas = document.createElement('canvas');
      var context = canvas.getContext('2d');
      var width = sourceCanvas.width;
      var height = sourceCanvas.height;

      canvas.width = width;
      canvas.height = height;
      context.imageSmoothingEnabled = true;
      context.drawImage(sourceCanvas, 0, 0, width, height);
      context.globalCompositeOperation = 'destination-in';
      context.beginPath();
      context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI, true);
      context.fill();
      return canvas;
    }
input.addEventListener('change', function(e) {
    const files = e.target.files;
    var croppable = false;
    const done = function(url) {
        input.value = ''; // Reset input
        preview.src = url;
        preview.style.display = 'block';
        cropButton.style.display='block';
        
        // Destroy existing cropper instance if it exists
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(preview, {
        aspectRatio: 1,
        viewMode: 1,
        ready: function () {
          croppable = true;
        },
      });
    };

    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function(event) {
            done(reader.result);
        };
        reader.readAsDataURL(files[0]);
    }
    
   
});

cropButton.addEventListener('click', function() {
    if (cropper) {
        // Get the canvas from cropper
        const canvas = cropper.getCroppedCanvas();
        roundedCanvas = getRoundedCanvas(canvas);
        // Convert to Base64 string and put it in the hidden input
        croppedInput.value = roundedCanvas.toDataURL();
    }
});
</script>