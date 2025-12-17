<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$id = $_SESSION['user_id'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$articles = $conn->query("SELECT * FROM `article` WHERE `status` = 'draft' AND `author_id` = $id ORDER BY `updated_at` DESC;")->fetchAll(PDO::FETCH_ASSOC);
$in_review_articles = $conn->query("SELECT * FROM `article` WHERE `status` = 'review' AND `author_id` = $id ORDER BY `updated_at` DESC;")->fetchAll(PDO::FETCH_ASSOC);
$published_articles = $conn->query("SELECT * FROM `article` WHERE `status` = 'published' AND `author_id` = $id;")->fetchAll(PDO::FETCH_ASSOC);
$rejected_articles = $conn->query("SELECT * FROM `article` WHERE `status` = 'rejected' AND `author_id` = $id;")->fetchAll(PDO::FETCH_ASSOC);
$submitted_articles = $conn->query("SELECT * FROM `article` WHERE `status` = 'submitted' AND `author_id` = $id;")->fetchAll(PDO::FETCH_ASSOC);

?>
<link rel="stylesheet" type="text/css" href="<?php baseurl("css/jquery.dataTables.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?php baseurl("css/buttons.dataTables.min.css") ?>">
<script src="<?php baseurl("js/jquery.min.js") ?>"></script>
<script src="<?php baseurl("js/jquery.dataTables.min.js") ?>"></script>
<script src="<?php baseurl("js/dataTables.buttons.min.js") ?>"></script>

<style>
    .dashboard-summary {
        padding: 30px;
        overflow-x: auto;
        overflow-y: hidden;
        display: flex;
        gap: 2rem;
        margin-bottom: 2.5rem;
        flex-wrap: nowrap;
    }

    .dashboard-card {
        background: linear-gradient(135deg, #e3f0ff 0%, #f9f9f9 100%);
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        padding: 2rem 2.5rem 1.5rem 2.5rem;
        min-width: 250px;
        flex: 1 1 220px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        position: relative;
    }

    .dashboard-card .icon {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
        color: #1976d2;
    }

    .dashboard-card .count {
        font-size: 2.3rem;
        font-weight: 700;
        color: #1976d2;
    }

    .dashboard-card .label {
        font-size: 1.1rem;
        color: #333;
        opacity: 0.85;
    }

    .section-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        margin-bottom: 2.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.2rem;
    }

    .section-header h2 {
        font-size: 1.35rem;
        font-weight: 600;
        margin: 0;
        color: #1976d2;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .add-btn {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 1rem;
        padding: 0.5rem 1.1rem;
        border-radius: 8px;
        background: #1976d2;
        color: #fff;
        border: none;
        transition: background 0.2s;
    }

    .add-btn:hover {
        background: #1256a3;
    }

    .table-responsive {
        overflow-x: auto;
        width: 100%;
    }
</style>
<div class="container"
    style="height: 100vh; padding-top: 32px; padding-bottom: 32px;margin-bottom: 40px;">
    <!-- Summary Cards -->
    <div class="dashboard-summary">
        <div class="dashboard-card">
            <span class="icon">📝</span>
            <span class="count"><?php echo count($articles); ?></span>
            <span class="label">Draft Articles</span>
        </div>
        <div class="dashboard-card">
            <span class="icon">✔</span>
            <span class="count"><?php echo count($submitted_articles); ?></span>
            <span class="label">Submitted Articles</span>
        </div>
        <div class="dashboard-card">
            <span class="icon">⏳</span>
            <span class="count"><?php echo count($in_review_articles); ?></span>
            <span class="label">In-Review Articles</span>
        </div>
        <div class="dashboard-card">
            <span class="icon">✅</span>
            <span class="count"><?php echo count($published_articles); ?></span>
            <span class="label">Published Articles</span>
        </div>
        <div class="dashboard-card">
            <span class="icon">❌</span>
            <span class="count"><?php echo count($rejected_articles); ?></span>
            <span class="label">Rejected Articles</span>
        </div>
    </div>
    <!-- Published Articles Section -->
    <div class="section-card">
        <div class="section-header">
            <h2>✅ Published Articles</h2>
        </div>
        <div class="table-responsive">
            <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Article Id</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($published_articles as $article) { ?>
                        <tr>
                            <td><?php echo $article['article_id'] ?></td>
                            <td><?php echo $article['title'] ?></td>
                            <td><?php echo $article['category'] ?></td>
                            <td><?php echo $article['updated_at'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="window.location.href='?page=edit_article&id=<?php echo $article['article_id'] ?>&mode=published'">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="window.location.href='components/api/delete.php?id=<?php echo $article['article_id'] ?>&type=article'">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="section-card">
        <div class="section-header">
            <h2>✔ Submitted Articles</h2>
        </div>
        <div class="table-responsive">
            <table id="exampleIn1" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Article Id</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submitted_articles as $article) { ?>
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
    <!-- In-Review Articles Section -->
    <div class="section-card">
        <div class="section-header">
            <h2>⏳ In-Review Articles</h2>
        </div>
        <div class="table-responsive">
            <table id="exampleIn" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Article Id</th>
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
    <div class="section-card">
        <div class="section-header">
            <h2>❌ Rejected Articles</h2>
        </div>
        <div class="table-responsive">
            <table id="examplere" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                <thead>
                    <tr>
                        <th>Article Id</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejected_articles as $article) { ?>
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
        $("#exampleIn1").DataTable({
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
    $(document).ready(function () {
        $("#examplere").DataTable({
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