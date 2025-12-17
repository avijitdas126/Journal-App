<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$id=$_SESSION['user_id'];
$sql="SELECT article_id,title,category,submitted_at FROM `article` WHERE `status` = 'submitted' ORDER BY `submitted_at` DESC;";
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$articles=$conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);   
$sql="SELECT article_id,title,category,submitted_at FROM `article` WHERE `status` = 'review' AND article_id IN (SELECT article_id FROM reviews WHERE reviewer_id = :id)  ORDER BY `submitted_at` DESC;";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$inarticles=$stmt->fetchAll(PDO::FETCH_ASSOC);   
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
                List of Non-Review Articles
            </h1>
        </div>
    </div>
    <div class="row py-4">
        <div class="col-12">
            <div style="overflow-x:auto; width:100%">
                <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Submited</th>
                        <th>Actions</th>
                    </tr>

                </thead>
                <tbody>
                   <?php foreach($articles as $article){ ?>
                    <tr>
                        <td><?php echo $article['title'] ?></td>
                        <td><?php echo $article['category'] ?></td>
                        <td><?php echo $article['submitted_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="window.location.href='?page=add_review&id=<?php echo $article['article_id'] ?>'">Make a Review</button>
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
        <div class="col-12" style="margin-bottom: 40px;">
            <div style="overflow-x:auto; width:100%">
                <table id="exampleIn" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Submited</th>
                        <th>Actions</th>
                    </tr>

                </thead>
                <tbody>
                   <?php foreach($inarticles as $article){ ?>
                    <tr>
                        <td><?php echo $article['title'] ?></td>
                        <td><?php echo $article['category'] ?></td>
                        <td><?php echo $article['submitted_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="window.location.href='?page=in_review&id=<?php echo $article['article_id'] ?>'">Make a Review</button>
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

</script>