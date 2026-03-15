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
    $topThree = array_slice($leaders, 0, 3);
    $currentUserId = $_SESSION['user_id'] ?? null;
    $currentUserRole = $_SESSION['role'] ?? null;
    $currentUserRank = null;
    $currentLeader = null;

    foreach ($leaders as $index => $leader) {
        if ((string) $leader['user_id'] === (string) $currentUserId && (string) $leader['user_type'] === (string) $currentUserRole) {
            $currentUserRank = $index + 1;
            $currentLeader = $leader;
            break;
        }
    }

    $visibleLeaders = array_slice($leaders, 3, 7);
    $showPinnedCurrentUser = $currentUserRank !== null && $currentUserRank > 10 && $currentLeader !== null;
    ?>

    <style>
        .leaderboard-shell {
            min-height: calc(100vh - 100px);
            display: flex;
            justify-content: center;
            padding: 2.5rem 1rem;
            background: radial-gradient(circle at top left, #f9ecff 0%, #f4f7fc 40%, #f6f7fb 100%);
        }

        .leaderboard-card {
            width: 100%;
            max-width: 760px;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(37, 34, 63, 0.15);
            overflow: hidden;
        }

        .leaderboard-head {
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f0f1f7;
            color: #a01786;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .leaderboard-title {
            margin: 0;
            font-size: 1.15rem;
            color: #8e136f;
        }

        .top-performers {
            display: flex;
            justify-content: center;
            gap: 1.25rem;
            padding: 1.2rem 1rem 1rem;
            border-bottom: 1px solid #f0f1f7;
            align-items: flex-end;
        }

        .performer {
            text-align: center;
            min-width: 100px;
        }

        .performer.is-champion {
            transform: translateY(-12px);
        }

        .performer.current-user .name::after {
            content: 'You';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 0.35rem;
            padding: 0.08rem 0.4rem;
            border-radius: 999px;
            background: #ffe5f6;
            color: #b50d79;
            font-size: 0.68rem;
            font-weight: 700;
            vertical-align: middle;
        }

        .avatar {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            margin: 0 auto 0.45rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            border: 3px solid #fff;
            box-shadow: 0 8px 16px rgba(20, 20, 50, 0.18);
        }

        .avatar.rank-1 {
            background: linear-gradient(135deg, #ff2ca2, #b70979);
            width: 78px;
            height: 78px;
        }

        .avatar.rank-2 {
            background: linear-gradient(135deg, #2fc4ff, #1268b0);
        }

        .avatar.rank-3 {
            background: linear-gradient(135deg, #f9a13a, #d16b07);
        }

        .performer .name {
            font-size: 0.88rem;
            color: #212741;
            font-weight: 600;
            margin-bottom: 0.15rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .performer .points {
            font-size: 0.76rem;
            color: #8f2f7b;
            font-weight: 600;
        }

        .performer .pub {
            font-size: 0.66rem;
            font-weight: 700;
            color: #000000;
            font-weight: 600;
        }

        .rank-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .rank-row {
            display: grid;
            grid-template-columns: 40px 42px auto auto auto;
            align-items: center;
            gap: 0.65rem;
            padding: 0.78rem 1.1rem;
            border-bottom: 1px solid #f2f3f7;
            font-size: 0.9rem;
            color: #31344f;
            transition: background 0.2s ease;
        }

        .rank-row:hover {
            background: #fafbff;
        }

        .rank-row.current-user {
            background: linear-gradient(90deg, #ffeaf6, #fff4fb);
            border-left: 4px solid #f1098f;
        }

        .rank-number {
            color: #6c748f;
            font-weight: 700;
            text-align: center;
        }

        .mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #7e8eb3, #5a6585);
            color: #fff;
            font-size: 0.74rem;
            font-weight: 700;
        }

        .leader-name {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 0;
        }

        .leader-name a {
            color: #2c3149;
            text-decoration: none;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .leader-name a:hover {
            color: #8e136f;
        }

        .leader-points {
            color: #a31781;
            font-weight: 700;
            font-size: 0.83rem;
            white-space: nowrap;
        }

        .rank-row.tail-pink {
            background: linear-gradient(90deg, #ff1594, #e50084);
            color: #fff;
        }

        .rank-row.tail-cyan {
            background: linear-gradient(90deg, #17bff3, #00a4df);
            color: #fff;
        }

        .rank-row.tail-pink .rank-number,
        .rank-row.tail-cyan .rank-number,
        .rank-row.tail-pink .leader-name a,
        .rank-row.tail-cyan .leader-name a,
        .rank-row.tail-pink .leader-points,
        .rank-row.tail-cyan .leader-points {
            color: #fff;
        }

        .rank-row.tail-pink .mini-avatar,
        .rank-row.tail-cyan .mini-avatar {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.45);
        }

        .empty-board {
            padding: 2rem 1.25rem;
            text-align: center;
            color: #7680a0;
            font-weight: 500;
        }

        .leaderboard-divider {
            padding: 0.7rem 1.1rem;
            background: #fbecf7;
            color: #a31781;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-top: 1px solid #f5d3e9;
            border-bottom: 1px solid #f5d3e9;
        }

        @media (max-width: 640px) {
            .leaderboard-head {
                padding: 1rem;
            }

            .top-performers {
                gap: 0.6rem;
                padding: 1rem 0.5rem;
            }

            .performer {
                min-width: 90px;
            }

            .performer.is-champion {
                transform: translateY(-8px);
            }

            .rank-row {
                grid-template-columns: 30px 32px 1fr auto;
                gap: 0.45rem;
                padding: 0.72rem 0.8rem;
            }
        }
    </style>

    <div class="leaderboard-shell">
        <div class="leaderboard-card">
            <div class="leaderboard-head">

                <h1 class="leaderboard-title">Leaderboard</h1>

            </div>

            <?php if (!empty($topThree)) { ?>
                <div class="top-performers">
                    <?php
                    $podiumOrder = [1, 0, 2];
                    foreach ($podiumOrder as $podiumIndex) {
                        if (!isset($topThree[$podiumIndex])) {
                            continue;
                        }
                        $leader = $topThree[$podiumIndex];
                        $rank = $podiumIndex + 1;
                        $initial = strtoupper(substr(trim($leader['name']), 0, 1));
                        $isCurrentUser = $currentUserRank === $rank;
                        ?>
                        <div
                            class="performer <?php echo $rank === 1 ? 'is-champion' : ''; ?> <?php echo $isCurrentUser ? 'current-user' : ''; ?>">
                            <div class="avatar rank-<?php echo $rank; ?>"><?php echo htmlspecialchars($initial); ?></div>

                            <div class="name"><?php echo htmlspecialchars($leader['name']); ?></div>
                            <div class="pub"><?php echo htmlspecialchars($leader['published_articles']); ?> Articles Published</div>
                            <div class="points"><?php echo htmlspecialchars($leader['final_marks']); ?> pts</div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (count($leaders) > 0) { ?>
                <ul class="rank-list">
                    <?php
                    $tailStartRank = max(4, count($visibleLeaders) + 3 - 2);
                    foreach ($visibleLeaders as $index => $leader) {
                        $rank = $index + 4;

                        $tailClass = '';
                        if ($rank >= $tailStartRank && count($visibleLeaders) >= 3) {
                            $offset = $rank - $tailStartRank;
                            $tailClass = $offset === 1 ? 'tail-cyan' : 'tail-pink';
                        }

                        $initial = strtoupper(substr(trim($leader['name']), 0, 1));
                        $isCurrentUser = $currentUserRank === $rank;
                        ?>
                        <li class="rank-row <?php echo $tailClass; ?> <?php echo $isCurrentUser ? 'current-user' : ''; ?>">
                            <span class="rank-number"><?php echo $rank; ?></span>
                            <span class="mini-avatar"><?php echo htmlspecialchars($initial); ?></span>

                            <span class="leader-name">
                                <a href="<?php baseurl('profile.php') ?>?username=<?php echo urlencode($leader['username']); ?>">
                                    <?php echo htmlspecialchars($leader['name']); ?>
                                </a>
                            </span>
                            <span class="pub"><?php echo htmlspecialchars($leader['published_articles']); ?> Articles
                                Published</span>

                            <span class="leader-points"><?php echo htmlspecialchars($leader['final_marks']); ?> pts</span>
                        </li>
                    <?php } ?>

                    <?php if ($showPinnedCurrentUser) {
                        $initial = strtoupper(substr(trim($currentLeader['name']), 0, 1));
                        ?>
                        <li class="leaderboard-divider">Your current rank</li>
                        <li class="rank-row current-user tail-pink">
                            <span class="rank-number"><?php echo $currentUserRank; ?></span>
                            <span class="mini-avatar"><?php echo htmlspecialchars($initial); ?></span>
                            <span class="leader-name">
                                <a
                                    href="<?php baseurl('profile.php') ?>?username=<?php echo urlencode($currentLeader['username']); ?>">
                                    <?php echo htmlspecialchars($currentLeader['name']); ?>
                                </a>
                            </span>
                            <span class="pub"><?php echo htmlspecialchars($leader['published_articles']); ?> Articles
                                Published</span>

                            <span class="leader-points"><?php echo htmlspecialchars($currentLeader['final_marks']); ?> pts</span>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <div class="empty-board">No ranking data found yet.</div>
            <?php } ?>
        </div>
    </div>
    <?php
}
?>