<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$id = $_SESSION['user_id'];
$sql = "SELECT * FROM `article` WHERE `status` = 'draft' AND `author_id` = $id ORDER BY `updated_at` DESC;";
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$articles = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT * FROM `article` WHERE `status` = 'review' AND `author_id` = $id ORDER BY `updated_at` DESC;";
$in_review_articles = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>
<link rel="stylesheet" type="text/css" href="<?php baseurl("css/jquery.dataTables.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?php baseurl("css/buttons.dataTables.min.css") ?>">
<script src="<?php baseurl("js/jquery.min.js") ?>"></script>
<script src="<?php baseurl("js/jquery.dataTables.min.js") ?>"></script>
<script src="<?php baseurl("js/dataTables.buttons.min.js") ?>"></script>

<div class="container" style="height: 100vh;padding-top:20px;">
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h1>
                List of draft Articles
            </h1>
            <button type="button" class="btn btn-primary d-flex gap-1" style="align-items: center;"
                onclick="window.location.href='?page=add_article&id='+Date.now()"><svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ffffff" class="bi bi-plus-lg"
                    viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                </svg>Add Article</button>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-12">
            <div style="overflow-x:auto; width:100%">
                <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                    <thead>
                        <tr>
                            <th>Article Id </th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date Updated</th>
                            <th>Actions</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article) { ?>
                            <tr>
                                <td><?php echo $article['article_id'] ?></td>
                                <td><?php echo $article['title'] ?></td>
                                <td><?php echo $article['category'] ?></td>
                                <td><?php echo $article['updated_at'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="window.location.href='?page=edit_article&id=<?php echo $article['article_id'] ?>'">Edit</button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="deleteArticle(<?php echo $article['article_id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row py-2">
        <div class="col-12 d-flex" style="justify-content: space-between;align-items: center;">
            <h1>
                List of In-Review Articles
            </h1>

        </div>
    </div>
    <div class="row py-4" style="margin-bottom: 140px;">
        <div class="col-12">
            <div style="overflow-x:auto; width:100%">
                <table id="exampleIn" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                    <thead>
                        <tr>
                            <th>Article Id </th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date Updated</th>
                            <th>Actions</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php foreach ($in_review_articles as $article) { ?>
                            <tr>
                                <td><?php echo $article['article_id'] ?></td>
                                <td><?php echo $article['title'] ?></td>
                                <td><?php echo $article['category'] ?></td>
                                <td><?php echo $article['updated_at'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary"
                                        onclick="window.location.href='?page=edit_review_article&id=<?php echo $article['article_id'] ?>'">Edit</button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="deleteArticle(<?php echo $article['article_id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
    $(document).ready(function () {
        $("#exampleIn").DataTable({
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