<?php

require_once __DIR__ . '/../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

if ($method === 'POST') {

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $title = $_POST['notice_title'];
    $uploadDir = __DIR__ . "/../../uploads/notices/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['notice_file']['name'])) {


        $fileName = basename($_FILES['notice_file']['name']);
        $ext = explode('.', $fileName)[1];

        if ($ext != 'pdf') {
            header("Location: /Journal/views/dashboard.php?page=notice&status=invalid_file_type&message=invalid_file_type");
            exit;
        }

        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        move_uploaded_file($_FILES['notice_file']['tmp_name'], $uploadDir . $filename);

        $url = "http://localhost/Journal/uploads/notices/" . $filename;

        $stmt = $conn->prepare(
            "INSERT INTO notices (title, url, author_id, at_publish) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$title, $url, $_SESSION['user_id']]);

        header("Location: /Journal/views/dashboard.php?page=notice&status=success");
        exit;
    }
} else {
    $error = $_GET['status'] ?? '';
    if ($error == 'success') {
        ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> New Notice added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    } else if ($error == 'error') {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was an error adding the new Notice.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
    } else if ($error == 'invalid_file_type') {
        ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Warning!</strong> Invalid file type. Please upload a PDF file.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php
    }else if($error=='delete_success'){ ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Notice deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } else if($error=='delete_error'){ ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was an error deleting Notice.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
    <?php }
    $sql = "SELECT n.id,n.title,a.name,n.at_publish FROM `notices` n JOIN admins a ON a.admin_id =n.author_id ORDER BY n.at_publish DESC;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                List of Notices
            </h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add New Notice</button>
        </div>
    </div>
    <div class="row py-4" style="margin-bottom: 140px;">
        <div class="col-12" style="margin-bottom: 40px;">
            <div style="overflow-x:auto; width:100%">
                <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                    <thead>
                        <tr>
                            <th> Id </th>
                            <th>Title</th>
                            <th>Author Name</th>
                            <th>Publish At</th>
                            <th>Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php foreach ($notices as $notice) { ?>
                            <tr>
                                <td><?php echo $notice['id'] ?></td>
                                <td><?php echo $notice['title'] ?></td>
                                <td><?php echo $notice['name'] ?></td>
                                <td><?php echo $notice['at_publish'] ?></td>
                                <td>
                                    <a href="components/api/delete.php?id=<?php echo $notice['id'] ?>&type=notice"><button class="btn btn-sm btn-danger"
                                        >Delete</button></a>
                                </td>
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Notice</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row py-4">
                        <form action="/Journal/views/components/notice.php" method="POST" enctype="multipart/form-data">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notice_title" class="form-label">Notice Title</label>
                                    <input type="text" class="form-control" id="notice_title"
                                        placeholder="Enter a notice title" name="notice_title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="notice_file" class="form-label">Notice File</label>
                                    <input type="file" class="form-control" id="notice_file" name="notice_file"
                                        required>
                                </div>

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