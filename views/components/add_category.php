<?php
ini_set('display_errors', 1);
require_once __DIR__ . '/../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$editModel = [];
if ($method === 'POST') {
    $isEdit = $_POST['isEdit'] == 'true' ? true : false;
    if ($isEdit) {
        $id = $_POST['id'];
        $category_name = $_POST['category_name'];
        $category_slug = $_POST['category_slug'];
        $category_description = $_POST['category_description'];
        print_r($_POST);
        try {
            $sql = "UPDATE category 
            SET category = :category, slug = :slug, description = :description
            WHERE id = :id;";

            $stmt = $conn->prepare($sql);
            $success = $stmt->execute([
                ':category' => $category_name,
                ':slug' => $category_slug,
                ':description' => $category_description,
                ':id' => $id
            ]);

            if ($success) {
                header("Location: /Journal/views/dashboard.php?page=add_category&status=$success");
                exit;
            } else {
                header("Location: /Journal/views/dashboard.php?page=add_category&status=error");
                exit;
            }

        } catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
            exit;
        }
    }
    $category_name = $_POST['category_name'];
    $category_slug = $_POST['category_slug'];
    $category_description = $_POST['category_description'];

    try {
        $sql = "SELECT * FROM `category` WHERE `category` = :category ;";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':category' => $category_name]);
        if (count($stmt->fetchAll())) {
            header("Location: /Journal/views/dashboard.php?page=add_category&status=exists");
            exit;
        }
        $sql = "INSERT INTO category 
            (category, slug, description)
            VALUES (:category, :slug, :description);";

        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            ':category' => $category_name,
            ':slug' => $category_slug,
            ':description' => $category_description
        ]);

        if ($success) {
            header("Location: /Journal/views/dashboard.php?page=add_category&status=success");
            exit;
        } else {
            header("Location: /Journal/views/dashboard.php?page=add_category&status=error");
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
            <strong>Success!</strong> New Category added successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
    } else if ($error == 'error') {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> There was an error adding the new category.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
    } else if ($error == 'exists') {
        ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Warning!</strong> Category already exists. Please choose a different category.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php
    }
    $sql = "SELECT * FROM `category`;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();

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

<div class="container" style="height: 100vh;padding-top:20px;">
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h1>
                List of Categories
            </h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add New Category</button>
        </div>
    </div>
    <div class="row py-4" style="margin-bottom: 140px;">
        <div class="col-12" style="margin-bottom: 40px;">
            <div style="overflow-x:auto; width:100%">
                <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                    <thead>
                        <tr>
                            <th>Category Id </th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category) { ?>
                            <tr>
                                <td><?php echo $category['id'] ?></td>
                                <td><?php echo $category['category'] ?></td>
                                <td><?php echo $category['slug'] ?></td>
                                <td><?php echo $category['description'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" 
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal1"
                                        data-id="<?php echo htmlspecialchars($category['id']); ?>"
                                        data-category="<?php echo htmlspecialchars($category['category']); ?>"
                                        data-slug="<?php echo htmlspecialchars($category['slug']); ?>"
                                        data-description="<?php echo htmlspecialchars($category['description']); ?>">
                                        Edit
                                    </button>
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row py-4">
                        <form action="/Journal/views/components/add_category.php" method="POST">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="hidden" name="isEdit" value="false">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="category_name"
                                        placeholder="Enter a category name" name="category_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="category_slug"
                                        placeholder="Enter a slug" name="category_slug" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="category_description" rows="3"
                                        placeholder="Enter a description" name="category_description"
                                        required></textarea>
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
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Category</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row py-4">
                        <form action="/Journal/views/components/add_category.php" method="POST">
                            <div class="col-12">
                                <div class="mb-3">
                                    <input type="hidden" name="isEdit" value="true">
                                    <input type="hidden" name="id" id="id" value="">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="edit_category_name"
                                        value=""
                                        placeholder="Enter a category name" name="category_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category_slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="edit_category_slug"
                                        value="" placeholder="Enter a slug"
                                        name="category_slug" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_category_description" rows="3"
                                        placeholder="Enter a description" name="category_description"
                                        required></textarea>
                                <script>
                                    // Populate edit modal with selected category data
                                    $(document).on('click', '.edit-btn', function () {
                                        const btn = $(this);
                                        $('#id').val(btn.data('id'));
                                        $('#edit_category_name').val(btn.data('category'));
                                        $('#edit_category_slug').val(btn.data('slug'));
                                        $('#edit_category_description').val(btn.data('description'));
                                    });
                                </script>
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