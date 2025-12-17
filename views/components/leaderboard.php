<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$method = $_SERVER['REQUEST_METHOD'];
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));

if ($method === 'GET') {
    $stmt = $conn->prepare("SELECT
    u.user_id,
    u.name,
    u.username,
    u.user_type,
    COUNT(DISTINCT a.article_id) AS published_articles,
    COALESCE(SUM(lr.marks),0) AS final_marks,
    (COUNT(DISTINCT a.article_id) * 10 + COALESCE(SUM(lr.marks),0)) AS score
FROM (
    SELECT 
        admin_id AS user_id,
        name,
        username,
        role AS user_type
    FROM admins
    UNION ALL
    SELECT 
        user_id,
        name,
        username,
        'student' AS user_type
    FROM students
) u
LEFT JOIN article a
    ON a.author_id = u.user_id
    AND a.author_type = u.user_type
    AND a.status = 'published'
LEFT JOIN (
    SELECT r1.article_id, r1.marks
    FROM reviews r1
    JOIN (
        SELECT article_id, MAX(created_at) AS last_review_time
        FROM reviews
        GROUP BY article_id
    ) r2
        ON r1.article_id = r2.article_id
        AND r1.created_at = r2.last_review_time
) lr
    ON lr.article_id = a.article_id
GROUP BY 
    u.user_id,
    u.user_type,
    u.name,
    u.username
HAVING published_articles > 0
ORDER BY score DESC;
");
    $stmt->execute();
    $leaders = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    Leaderboard - Top 10 Users
                </h1>
            </div>
        </div>
        <div class="row py-4" style="margin-bottom: 140px;">
            <div class="col-12" style="margin-bottom: 40px;">
                <div style="overflow-x:auto; width:100%">
                    <table id="example" class="table table-hover responsive nowrap" style="width:100%; min-width:600px;">
                        <thead>
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Name</th>
                                <th scope="col">Total Points</th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            foreach ($leaders as $leader) { ?>
                                <tr>
                                    <th scope="row"><?php echo $rank++; ?></th>
                                    <td><a href="http://localhost/Journal/views/profile.php?username=<?php echo $leader['username'] ?>"><?php echo htmlspecialchars($leader['name']); ?></a></td>
                                    <td><?php echo htmlspecialchars($leader['final_marks']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
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