<?php
require_once __DIR__ . '/../../utils/db_conn.php';
$conn = db_conn(Env('servername'), Env('db'), Env('username'), Env('password'));
$sql1 = "SELECT * FROM notices ORDER BY at_publish DESC;";
$stmt1 = $conn->prepare($sql1);
$stmt1->execute();
$notices = $stmt1->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    .notices-table {
        border-collapse: collapse;
        width: 100%;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .notices-table thead {
        background: linear-gradient(90deg, #1976d2 0%, #42a5f5 100%);
        color: white;
    }
    .notices-table th {
        padding: 15px 20px;
        text-align: left;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .notices-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    .notices-table tbody tr {
        transition: background 0.2s;
    }
    .notices-table tbody tr:hover {
        background-color: #f9f9f9;
    }
    .notice-title {
        font-weight: 600;
        color: #1976d2;
        margin: 0;
    }
    .notice-date {
        color: #999;
        font-size: 0.9rem;
    }
    .btn-download {
        background-color: #1976d2;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-download:hover {
        background-color: #1565c0;
        text-decoration: none;
    }
    .btn-download svg {
        width: 16px;
        height: 16px;
    }
    .empty-message {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
</style>

<div class="container my-4">
    <h2 class="mb-4">Notices</h2>
    <?php if (count($notices) === 0) { ?>
        <div class="empty-message">
            <p>No notices available at the moment.</p>
        </div>
    <?php } else { ?>
        <table class="notices-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Published Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notices as $notice) { ?>
                    <tr>
                        <td>
                            <p class="notice-title"><?php echo htmlspecialchars($notice['title']); ?></p>
                        </td>
                        <td>
                            <span class="notice-date"><?php echo date('d M Y, H:i', strtotime($notice['at_publish'])); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo htmlspecialchars($notice['url']); ?>" target="_blank" class="btn-download">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-pdf" viewBox="0 0 16 16">
                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1" />
                                    <path d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                                </svg>
                                Download
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>