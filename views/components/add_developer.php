<?php

require_once __DIR__ . '/../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

if ($method === 'POST') {

    $developer_name = $_POST['developer_name'];
    $developer_username = $_POST['developer_username'];
    $email = $_POST['email'];
    $developer_password = password_hash($_POST['developer_password'], PASSWORD_BCRYPT);
    $college_name = $_POST['college_name'];
    $department_id = $_POST['department_id'];

    try {
        $sql = "SELECT * FROM `admins` WHERE `username` = :username AND `role` = 'developer';";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':username' => $developer_username]);
        if (count($stmt->fetchAll())) {
            header("Location: /Journal/views/dashboard.php?page=add_developer&status=exists");
            exit;
        }
        $sql = "INSERT INTO admins 
            (name, username, password, college_name, department_id, role)
            VALUES (:name, :username, :password, :college_name, :department_id, 'developer');";

        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            ':name' => $developer_name,
            ':username' => $developer_username,
            ':password' => $developer_password,
            ':college_name' => $college_name,
            ':department_id' => $department_id
        ]);

        if ($success) {
            include __DIR__ . "/../../mail.php";
            $body = '
            <h1>Welcome to Journal</h1>
            <p>Your account has been created successfully. Here are your login details:</p>
            <ul>
            <li><strong>Username:</strong> ' . $admin_username . '</li>
            <li><strong>Password:</strong> ' . $_POST['admin_password'] . '</li>';
            $altbody = 'Welcome to Journal. Your account has been created successfully. Here are your login details: Username: ' . $admin_username . ' Password: ' . $_POST['admin_password'];
            $subject = 'Welcome to Journal, ' . $admin_name;
            sendMailToNewAdmin($email, $admin_name, $subject, $body, $altbody);
            header("Location: /Journal/views/dashboard.php?page=add_developer&status=success");
            exit;
        } else {
            header("Location: /Journal/views/dashboard.php?page=add_developer&status=error");
            exit;
        }

    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage();
        exit;
    }
} else {
    $error = $_GET['status'] ?? '';
    if ($error == 'success') {
        ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> New developer added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    } else if ($error == 'error') {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was an error adding the new developer.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
    } else if ($error == 'exists') {
        ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Warning!</strong> Username already exists. Please choose a different username.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php
    }
    $sql = "SELECT * FROM `admins` WHERE `role` = 'developer';";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetchAll();

}
// If not POST → render modal page
?>
<!-- your modal HTML here -->



<link rel="stylesheet" type="text/css" href="<?php baseurl("css/jquery.dataTables.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?php baseurl("css/buttons.dataTables.min.css") ?>">
<script src="<?php baseurl("js/jquery.min.js") ?>"></script>
<script src="<?php baseurl("js/jquery.dataTables.min.js") ?>"></script>
<script src="<?php baseurl("js/dataTables.buttons.min.js") ?>"></script>
<script src="<?php baseurl('js/bootstrap.bundle.min.js') ?>"></script>
<!-- Button trigger modal -->

<div class="container" style="height: 100vh;padding-top:20px; overflow-y:scroll;">
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h1>
                List of Developers
            </h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add New Developer</button>
        </div>
    </div>
    <div class="row py-4" style="margin-bottom: 140px;">
        <div class="col-12" style="margin-bottom: 40px;">
            <div style="overflow-x:auto; width:100%">
                <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                    <thead>
                        <tr>
                            <th>Admin Id </th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>College</th>
                            <th>Department</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php foreach ($user as $admin) { ?>
                            <tr>
                                <td><?php echo $admin['admin_id'] ?></td>
                                <td><?php echo $admin['name'] ?></td>
                                <td><?php echo $admin['username'] ?></td>
                                <td><?php echo $admin['college_name'] ?></td>
                                <td><?php echo $admin['department_id'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Developer</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row py-4">
                        <form action="/Journal/views/components/add_developer.php" method="POST">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="developer_name" class="form-label">Developer Name</label>
                                    <input type="text" class="form-control" id="developer_name"
                                        placeholder="Enter a developer name" name="developer_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="developer_username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="developer_username"
                                        placeholder="Enter a username" name="developer_username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="developer_password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="developer_password"
                                        name="developer_password" placeholder="Enter a Password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">College Name:</label>
                                    <input class="form-control" type="text" placeholder="Enter College"
                                        id="college_name" required name="college_name" aria-label="nameHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Email:</label>
                                    <input class="form-control" type="email" placeholder="Enter Email" id="email"
                                        required name="email" aria-label="emailHelp" />
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputText1" class="form-label">Department:</label>
                                    <select class="form-select" aria-label="role" name="department_id"
                                        id="department_id" required>
                                        <?php
                                        $stmt = $conn->prepare("SELECT * FROM `departments`;");
                                        $stmt->execute();
                                        $depts = $stmt->fetchAll();
                                        foreach ($depts as $dept) {
                                            ?>
                                            <option value="<?php echo $dept['department_id'] ?>"><?php echo $dept['name'] ?>
                                                -
                                                <?php echo $dept['code'] ?>
                                            </option> <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#example").DataTable({
            aaSorting: [],
            responsive: true,

            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ]
        });

        $(".dataTables_filter input")
            .attr("placeholder", "Search here...")
            .css({
                width: "300px",
                padding: '5px',
                display: "inline-block"
            });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>